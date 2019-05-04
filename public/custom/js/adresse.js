$('body').on('submit', '#submit-adresse-form', function (e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var form = $(this);
    $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        data: form.serialize(),
        success: function(data)
        {
            if(data['id']) {
                $('#editAdresseModal'+data['id']).modal('hide');
                $('body').find('#adresse'+data['id']).replaceWith($(data.html));
            }else{
                $('#addAdresseModal').modal('hide');
                $('.block-list-adresse').append($(data.html)); // show response from the php script.
            }
            
            
        }
    });


});
$(".delete-adress").click(function(){  
    var url = $(this).data('url');
    var id = $(this).data('id');
    $.ajax({
        type: "POST",
        url: url,
        success: function(data)
        {
            console.log('adress deleted');
            $("#adresse"+id).hide();
        }
    });


});

