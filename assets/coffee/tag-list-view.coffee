Taggr = Taggr || {}

#Since the interaction on this part is not that complicated, once view can handle
#all the list items. If we needed more functionality per tag list item, then it might
#make sense to have individual views per tag item
class Taggr.TagListView extends Backbone.View

  initialize: () ->
    @ui = {}
    @template = _.template($('#list-items-tpl').html())

  events:
    'click': 'tagClicked'

  render: () =>
    data = tag_list: @collection
    rendered_html = @template(data)
    @$el.html(rendered_html)
    @ui.buttons = @$('button')

  tagClicked: (evt) =>
    $target = $(evt.target)
    #if the click happens to be on the span in the button
    if evt.target.tagName is 'SPAN'
      $target = $target.parent()

    @ui.buttons.removeClass('active')
    $target.addClass('active')
    $child = $target.find('.tag-title')
    tag = $child.text()
    @trigger 'tag:clicked', tag
    
    evt.stopPropagation()
