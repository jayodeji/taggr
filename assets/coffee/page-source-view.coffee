Taggr = Taggr || {}

#This is the view that does a lot of the heavy lifting in terms of showing the
#raw page source and showing the different tags and highlighing the tags as they are
#clicked.
class Taggr.PageSourceView extends Backbone.View

  initialize: () ->
    @setupUIElements()
    @setupTagList()

  events:
    'click #close-source': 'closeSourceView'

  setupUIElements: () ->
    @ui =
      source_page: @$('.source-container')
      tag_list: @$('.tag-list')
      title: @$('h3')

  #Since this is a basic application, we can just set up all
  #the views and collections once, and just keep resetting them as
  #needed.
  setupTagList: () ->
    @tag_list = new Backbone.Collection []
    @tag_list.comparator = (tag) -> -tag.get('count')

    @tag_list_view = new Taggr.TagListView {collection: @tag_list}
    @ui.tag_list.html(@tag_list_view.el)

    #Fired everytime a tag in the tag_list_view is clicked
    @listenTo @tag_list_view, 'tag:clicked', @highlight

  render: () ->
    @ui.source_page.text(@model.getFormattedHtmlSource())
    @tag_list.reset @model.getTagList()

    url = @model.get('url')
    if url
      title = "#{@tag_list.length} unique tags found for : <span>#{url}</span>"
      @ui.title.html(title)

    @tag_list_view.render()
    @

  closeSourceView: () =>
    @model.clear()
    @ui.source_page.html('')

  #highlight.js jqueyr plugin used here. Pretty complicated regex to basically match
  #all instances of tags with an open < and close >, it will match all characters within those two anchors.
  #for all strings. This is the same regex as used on the backend to parse the html
  highlight: (tag) =>
    regex_str = "(<\\s*#{tag}((\\s+\\w+(\\s*=\\s*(\".*?\"|'.*?'|[^'\">\\s]+))?)+\\s*|\\s*)?\\s*?\/*?\\s*?>|<\\s*\/{0,1}\\s*#{tag}\\s*>)|<\\s*#{tag}\\s*\/\\s*>"
    regex = new RegExp(regex_str, "gi")
    @ui.source_page.removeHighlight()
    @ui.source_page.highlight(regex)

    #perform scrolling of found element into view
    $first_highlight = @ui.source_page.find('span.highlight').first()
    if $first_highlight.length > 0
      $first_highlight[0].scrollIntoView({behavior: 'smooth'}) # use the element directly instead of jquery
