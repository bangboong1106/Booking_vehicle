$(document).ready(function() {
	
    var readURL = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                const result = e.target.result;
                var formData = new FormData();
                formData.append('file', $('input[type=file]')[0].files[0]); 
                $.ajax({
                    url: uploadUrl,  
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    success:function(data){
                        $('.profile-pic').attr('src', result);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
    
            reader.readAsDataURL(input.files[0]);
        }
    }
   
    $(".file-upload").on('change', function(){
        readURL(this);
    });
    
    $(".upload-button").on('click', function() {
       $(".file-upload").click();
    });
});