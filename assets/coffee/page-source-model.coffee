Taggr = Taggr || {}

class Taggr.PageSourceModel extends Backbone.Model

  urlRoot: '/pagesource'

  #The base 64 encoding makes it easier and more easily identifiable when used as
  #the resource id.
  url: () ->
    encoded_url = window.btoa @get('url')
    "#{@urlRoot}/#{encoded_url}"

  #Although not totally needed, for readability, it is a good idea to format
  #the html as much as we can. beautify-html.js plugin is used to do this
  getFormattedHtmlSource: () ->
    html = @get 'raw_page_source'
    options = indent_size: 2
    style_html html, options

  getTagList: () ->
    tag_list = @get 'tag_list'
    tag_list or []

  #Basic validation, that the url is in a particular format.
  #There is no validation that the url actually needs to a proper webpage.
  #error handling on the backend will take care of that
  validate: (attrs, options) ->
    url = attrs.url
    regex = new RegExp '^(ftp|http|https):\/\/[^ "]+$', 'gi'
    valid = regex.test(url)
    return if valid is true
    return 'url_error'
