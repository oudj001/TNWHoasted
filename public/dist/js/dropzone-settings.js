/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



Dropzone.options.myDropzone = {
    init: function() {
        myDropzone = this; // closure
        $('.upload-dropzone').click(function() {
            myDropzone.processQueue();
        });
    },
//    previewTemplate: document.querySelector('#preview-template').innerHTML,
    paramName: "file", // The name that will be used to transfer the file
    dictDefaultMessage: '<div class="uploadbutton"><span class="glyphicon glyphicon-plus-sign"></span><p id="instructions">Drop your files to upload to ...</p></div>',
    dictRemoveFile: '<span class="glyphicon glyphicon-remove"></span>',
    uploadMultiple: true,
    parallelUploads: 100,
    autoProcessQueue: false,
    addRemoveLinks: true,
    accept: function(file, done) {
        if (file.name == "..") {
            done("Naha, you don't.");
        }
        else {
            done();
            if ($('.dz-preview').length) {
                $('.dz-default').fadeIn();
                $('.confirmation-box').fadeIn();
            }
        }

    }
};

$(document).ready(function() {

});
