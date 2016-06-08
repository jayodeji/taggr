var Taggr,
  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
  hasProp = {}.hasOwnProperty;

Taggr = Taggr || {};

Taggr.TagListView = (function(superClass) {
  extend(TagListView, superClass);

  function TagListView() {
    this.tagClicked = bind(this.tagClicked, this);
    this.render = bind(this.render, this);
    return TagListView.__super__.constructor.apply(this, arguments);
  }

  TagListView.prototype.initialize = function() {
    this.ui = {};
    return this.template = _.template($('#list-items-tpl').html());
  };

  TagListView.prototype.events = {
    'click': 'tagClicked'
  };

  TagListView.prototype.render = function() {
    var data, rendered_html;
    data = {
      tag_list: this.collection
    };
    rendered_html = this.template(data);
    this.$el.html(rendered_html);
    return this.ui.buttons = this.$('button');
  };

  TagListView.prototype.tagClicked = function(evt) {
    var $child, $target, tag;
    $target = $(evt.target);
    if (evt.target.tagName === 'SPAN') {
      $target = $target.parent();
    }
    this.ui.buttons.removeClass('active');
    $target.addClass('active');
    $child = $target.find('.tag-title');
    tag = $child.text();
    this.trigger('tag:clicked', tag);
    return evt.stopPropagation();
  };

  return TagListView;

})(Backbone.View);

Taggr = Taggr || {};

Taggr.UrlEntryView = (function(superClass) {
  extend(UrlEntryView, superClass);

  function UrlEntryView() {
    this.load = bind(this.load, this);
    this.keyPressed = bind(this.keyPressed, this);
    return UrlEntryView.__super__.constructor.apply(this, arguments);
  }

  UrlEntryView.prototype.initialize = function() {
    this.ui = {
      input: this.$('#url-input'),
      alert: this.$('#messages')
    };
    return this.listenTo(this.model, 'invalid', (function(_this) {
      return function() {
        return _this.ui.input.addClass('error');
      };
    })(this));
  };

  UrlEntryView.prototype.events = {
    'click #load-button': 'load',
    'focus #url-input': 'keyPressed',
    'keypress #url-input': 'keyPressed'
  };

  UrlEntryView.prototype.render = function() {
    this.ui.input.val('');
    this.ui.input.focus();
    return this;
  };

  UrlEntryView.prototype.keyPressed = function(evt) {
    if (this.ui.input.hasClass('error')) {
      this.ui.input.removeClass('error');
      this.trigger('error:removed');
    }
    if (evt.keyCode === 13) {
      this.load();
      return evt.preventDefault();
    }
  };

  UrlEntryView.prototype.load = function() {
    var attrs;
    this.ui.input.removeClass('error');
    attrs = {
      url: this.ui.input.val().trim()
    };
    this.model.set(attrs, {
      silent: true,
      validate: true
    });
    if (this.model.validationError == null) {
      return this.model.fetch();
    }
  };

  return UrlEntryView;

})(Backbone.View);

Taggr = Taggr || {};

Taggr.PageSourceModel = (function(superClass) {
  extend(PageSourceModel, superClass);

  function PageSourceModel() {
    return PageSourceModel.__super__.constructor.apply(this, arguments);
  }

  PageSourceModel.prototype.urlRoot = '/pagesource';

  PageSourceModel.prototype.url = function() {
    var encoded_url;
    encoded_url = window.btoa(this.get('url'));
    return this.urlRoot + "/" + encoded_url;
  };

  PageSourceModel.prototype.getFormattedHtmlSource = function() {
    var html, options;
    html = this.get('raw_page_source');
    options = {
      indent_size: 2
    };
    return style_html(html, options);
  };

  PageSourceModel.prototype.getTagList = function() {
    var tag_list;
    tag_list = this.get('tag_list');
    return tag_list || [];
  };

  PageSourceModel.prototype.validate = function(attrs, options) {
    var regex, url, valid;
    url = attrs.url;
    regex = new RegExp('^(ftp|http|https):\/\/[^ "]+$', 'gi');
    valid = regex.test(url);
    if (valid === true) {
      return;
    }
    return 'url_error';
  };

  return PageSourceModel;

})(Backbone.Model);

Taggr = Taggr || {};

Taggr.PageSourceView = (function(superClass) {
  extend(PageSourceView, superClass);

  function PageSourceView() {
    this.highlight = bind(this.highlight, this);
    this.closeSourceView = bind(this.closeSourceView, this);
    return PageSourceView.__super__.constructor.apply(this, arguments);
  }

  PageSourceView.prototype.initialize = function() {
    this.setupUIElements();
    return this.setupTagList();
  };

  PageSourceView.prototype.events = {
    'click #close-source': 'closeSourceView'
  };

  PageSourceView.prototype.setupUIElements = function() {
    return this.ui = {
      source_page: this.$('.source-container'),
      tag_list: this.$('.tag-list'),
      title: this.$('h3')
    };
  };

  PageSourceView.prototype.setupTagList = function() {
    this.tag_list = new Backbone.Collection([]);
    this.tag_list.comparator = function(tag) {
      return -tag.get('count');
    };
    this.tag_list_view = new Taggr.TagListView({
      collection: this.tag_list
    });
    this.ui.tag_list.html(this.tag_list_view.el);
    return this.listenTo(this.tag_list_view, 'tag:clicked', this.highlight);
  };

  PageSourceView.prototype.render = function() {
    var title, url;
    this.ui.source_page.text(this.model.getFormattedHtmlSource());
    this.tag_list.reset(this.model.getTagList());
    url = this.model.get('url');
    if (url) {
      title = this.tag_list.length + " unique tags found for : <span>" + url + "</span>";
      this.ui.title.html(title);
    }
    this.tag_list_view.render();
    return this;
  };

  PageSourceView.prototype.closeSourceView = function() {
    this.model.clear();
    return this.ui.source_page.html('');
  };

  PageSourceView.prototype.highlight = function(tag) {
    var $first_highlight, regex, regex_str;
    regex_str = "(<\\s*" + tag + "((\\s+\\w+(\\s*=\\s*(\".*?\"|'.*?'|[^'\">\\s]+))?)+\\s*|\\s*)?\\s*?\/*?\\s*?>|<\\s*\/{0,1}\\s*" + tag + "\\s*>)|<\\s*" + tag + "\\s*\/\\s*>";
    regex = new RegExp(regex_str, "gi");
    this.ui.source_page.removeHighlight();
    this.ui.source_page.highlight(regex);
    $first_highlight = this.ui.source_page.find('span.highlight').first();
    if ($first_highlight.length > 0) {
      return $first_highlight[0].scrollIntoView({
        behavior: 'smooth'
      });
    }
  };

  return PageSourceView;

})(Backbone.View);

Taggr = Taggr || {};

Taggr.MainView = (function(superClass) {
  extend(MainView, superClass);

  function MainView() {
    this.showPageSuccessfullyLoaded = bind(this.showPageSuccessfullyLoaded, this);
    this.showLoading = bind(this.showLoading, this);
    this.dismissAlert = bind(this.dismissAlert, this);
    this.syncError = bind(this.syncError, this);
    this.showError = bind(this.showError, this);
    this.displaySource = bind(this.displaySource, this);
    this.toggleViews = bind(this.toggleViews, this);
    return MainView.__super__.constructor.apply(this, arguments);
  }

  MainView.prototype.initialize = function() {
    this.ui = {
      alert: this.$('#alert-message'),
      carousel: this.$('#content-carousel')
    };
    this.setupChildrenViews();
    this.setupTemplates();
    this.setupEventListeners();
    return this.url_entry_view.render();
  };

  MainView.prototype.setupChildrenViews = function() {
    var options;
    this.model = new Taggr.PageSourceModel({});
    this.url_entry_view = new Taggr.UrlEntryView({
      el: this.$('#url-entry'),
      model: this.model
    });
    this.page_source_view = new Taggr.PageSourceView({
      el: this.$('#page-source-view'),
      model: this.model
    });
    options = {
      interval: false
    };
    return this.ui.carousel.carousel(options);
  };

  MainView.prototype.setupTemplates = function() {
    this.alert_tpl = _.template($('#message-tpl').html());
    return this.loading_tpl = _.template($('#loading-msg-tpl').html());
  };

  MainView.prototype.setupEventListeners = function() {
    this.listenTo(this.model, 'change', this.toggleViews);
    this.listenTo(this.model, 'invalid', this.showError);
    this.listenTo(this.model, 'request', this.showLoading);
    this.listenTo(this.model, 'error', this.syncError);
    return this.listenTo(this.url_entry_view, 'error:removed', this.dismissAlert);
  };

  MainView.prototype.toggleViews = function() {
    if (this.model.isNew()) {
      return this.displayUrlEntry();
    } else {
      this.showPageSuccessfullyLoaded();
      return window.setTimeout(this.displaySource, 500);
    }
  };

  MainView.prototype.displayUrlEntry = function() {
    this.ui.carousel.carousel(0);
    return this.url_entry_view.render();
  };

  MainView.prototype.displaySource = function() {
    this.dismissAlert();
    this.ui.carousel.carousel(1);
    return this.page_source_view.render();
  };

  MainView.prototype.showError = function(model, error_msg) {
    var alert_html;
    if (_.isEmpty(error_msg) || error_msg === 'url_error') {
      error_msg = 'Url is invalid. Please enter a properly formatted url i.e (http://www.google.com)';
    }
    alert_html = this.alert_tpl({
      message: error_msg
    });
    return this.ui.alert.html(alert_html);
  };

  MainView.prototype.syncError = function(model, error_obj) {
    var error_msg, status;
    if (error_obj == null) {
      error_obj = {};
    }
    error_msg = null;
    if (_.isObject(error_obj) && error_obj.status) {
      status = parseFloat(error_obj.status);
      if (error_obj.status === 404) {
        error_msg = "The page could not be loaded. It might not lead to an existing web page";
      } else if (error_obj.status > 499) {
        error_msg = "An unknown error occurred. Please refresh and try again.";
      }
    }
    return this.showError(model, error_msg);
  };

  MainView.prototype.dismissAlert = function() {
    return this.ui.alert.html('');
  };

  MainView.prototype.showLoading = function() {
    var loading_html;
    this.dismissAlert();
    loading_html = this.loading_tpl({});
    return this.ui.alert.html(loading_html);
  };

  MainView.prototype.showPageSuccessfullyLoaded = function() {
    return this.ui.alert.find('.progress-bar').html('<span>Request successful</span>').removeClass('active').addClass('progress-bar-success');
  };

  return MainView;

})(Backbone.View);
