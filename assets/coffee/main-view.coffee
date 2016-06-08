Taggr = Taggr || {}

class Taggr.MainView extends Backbone.View

  initialize: () ->
    @ui =
      alert: @$('#alert-message')
      carousel: @$('#content-carousel')

    @setupChildrenViews()
    @setupTemplates()
    @setupEventListeners()

    @url_entry_view.render()


  setupChildrenViews: () ->
    @model = new Taggr.PageSourceModel {}
    @url_entry_view = new Taggr.UrlEntryView {el: @$('#url-entry'), model: @model}
    @page_source_view = new Taggr.PageSourceView {el: @$('#page-source-view'), model: @model}

    #Setup the carousel here also
    options = interval: false
    @ui.carousel.carousel(options)

  setupTemplates: () ->
    @alert_tpl = _.template($('#message-tpl').html())
    @loading_tpl = _.template($('#loading-msg-tpl').html())

  setupEventListeners: () ->
    @listenTo @model, 'change', @toggleViews
    @listenTo @model, 'invalid', @showError
    @listenTo @model, 'request', @showLoading
    @listenTo @model, 'error', @syncError
    @listenTo @url_entry_view, 'error:removed', @dismissAlert

  #A model can only be new if it does not have an id.
  #The only time a model will not have an id is when it has either been created,
  #or we have fetched a web page and cleared the model, in which case, we do want
  #to display the url entry view again
  toggleViews: () =>
    if @model.isNew()
      @displayUrlEntry()
    else
      @showPageSuccessfullyLoaded()
      window.setTimeout @displaySource, 500


  displayUrlEntry: () ->
    @ui.carousel.carousel(0)
    @url_entry_view.render()

  displaySource: () =>
    @dismissAlert()
    @ui.carousel.carousel(1)
    @page_source_view.render()

  showError: (model, error_msg) =>
    if _.isEmpty(error_msg) or error_msg is 'url_error'
      error_msg = 'Url is invalid. Please enter a properly formatted url i.e (http://www.google.com)'

    alert_html = @alert_tpl {message: error_msg}
    @ui.alert.html(alert_html)

  #If for some reason we returned from the backend with an
  #error code not in the 200 range
  syncError: (model, error_obj={}) =>
    error_msg = null
    if _.isObject(error_obj) and error_obj.status
      status = parseFloat(error_obj.status)
      if error_obj.status is 404
        error_msg = "The page could not be loaded. It might not lead to an existing web page"
      else if error_obj.status > 499
        error_msg = "An unknown error occurred. Please refresh and try again."
    
    @showError model, error_msg

  dismissAlert: () =>
    @ui.alert.html('')

  showLoading: () =>
    @dismissAlert()
    loading_html = @loading_tpl {}
    @ui.alert.html(loading_html)

  showPageSuccessfullyLoaded: () =>
    @ui.alert.find('.progress-bar')
            .html('<span>Request successful</span>')
            .removeClass('active')
            .addClass('progress-bar-success')
