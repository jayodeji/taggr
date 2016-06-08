Taggr = Taggr || {}

#First part of the carousel. This view holds the input box that the user will enter the
#url into.
class Taggr.UrlEntryView extends Backbone.View

  initialize: () ->
    @ui =
      input: @$('#url-input')
      alert: @$('#messages')

    #using a backbone model allows us to use the different events it fires.
    #The model takes care of frontend validation of the inputted url, and all
    #that is needed to be done is just listen to the invalid event if any and show
    #the appropriate ui for the error
    @listenTo @model, 'invalid', () => @ui.input.addClass('error')

  events:
    'click #load-button': 'load'
    'focus #url-input': 'keyPressed'
    'keypress #url-input': 'keyPressed'

  render: () ->
    @ui.input.val('')
    @ui.input.focus()
    @

  keyPressed: (evt) =>
    if @ui.input.hasClass('error')
      @ui.input.removeClass('error')
      @trigger 'error:removed'

    if evt.keyCode is 13
      @load()
      evt.preventDefault()

  #We are simulating as if a resource exists on our server with the id of the
  #url that needs to be fetched. This way the data we need to render can be represented as a model
  load: () =>
    @ui.input.removeClass('error')
    attrs = url: @ui.input.val().trim()
    @model.set attrs, {silent: true, validate: true}
    if !@model.validationError?
      @model.fetch()
