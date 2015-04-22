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
    dictDefaultMessage: 'Drop your files to upload to ...',
    dictRemoveFile: 'Remove the file',
    autoProcessQueue: false,
    addRemoveLinks: true,
    accept: function(file, done) {
        if (file.name == "test.png") {
            done("Naha, you don't.");
        }
        else {
            done();
        }

    }
};