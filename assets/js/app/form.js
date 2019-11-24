import $ from 'jquery'

/**
 * Display the form below the current comment
 */
const form = () => {
    $('.linkForm').click(function (e) {
        e.preventDefault()

        const $this = $(this)
        const thisParent = $this.parent()
        const form = $('#formComment')
        const comment = $this.parents('.comment')

        // the values of hidden fields of the current comment
        const id = thisParent.find('#commentId').val()
        const content = thisParent.find('#commentContent').val()

        // hide and show the form
        form.hide()
        comment.after(form)
        form.slideDown()

        // initialie the values of the form fields
        form.find('#commentId').prop('value', id)
        form.find('#comment_content').text(content)
        form.find('button[type="submit"]').text('Update a comment').addClass('mb-4')
    })
}

export default form
