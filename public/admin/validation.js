/*=====================================================================*/

   /**product validations*/
   $('form[name="product-form"]').validate({
      
    rules: {
       name: {required:true,minlength:2,maxlength:255},
       description: {required:true,minlength:2,maxlength:1000},
       quantity:{required:true,number:true,minlength:1,maxlength:10},
       size:{required:true},
       categories:{required:true}
    },

 });

/*=====================================================================*/

/*=====================================================================*/

  /**category validations*/
  $('form[name="category-form"]').validate({
      
    rules: {
       name: {required:true,minlength:2,maxlength:255},
       description: {required:true,minlength:2,maxlength:1000},
    },

 });

/*=====================================================================*/