<?php 
header("Content-type: text/javascript");
header("Expires: Fri, 01 Jan 2010 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
?>
var uploader = new plupload.Uploader({
	runtimes : 'html5,flash',
	containes: 'plupload',
	browse_button: 'browse',
	drop_element:"droparea",
	url : 'addfile',
	flash_swf_url:'./files/js/plupload/plupload.flash.swf',
	multipart : true,
	urlstream_upload:true,
	max_file_size:'16mb',
	});
	

uploader.bind('Init',function(up, params){
	if(params.runtime != 'html5'){
		$('#droparea').css('border','none').find('p,span').remove();
	} 
})

uploader.bind('UploadProgress',function(up, file){
	console.log(file);
	$('#'+file.id).find('.progress').css('width',file.percent+'%').attr("title", file.percent+'%');
})

uploader.init();

uploader.bind('FilesAdded',function(up,files){
	console.log(files);
	var filelist = $('#filelist');
	for(var i in files){
		var file = files[i]; 
		filelist.prepend('<div id="'+file.id+'" class="file">'+file.name+' ('+plupload.formatSize(file.size)+')'+'<div class="progressbar"><div class="progress"></div></div></div>');
	}

	$('#droparea').removeClass('hover')
	uploader.start();
	uploader.refresh();
});


uploader.bind('Error',function(up, err){
		console.log(err);
		alert(err.message);
		$('#droparea').removeClass('hover')
		uploader.refresh();
});

uploader.bind('FileUploaded',function(up, file, response){

console.log(file);
console.log(response);
	data = $.parseJSON(response.response);
	if(data.error){
		alert(data.message); 
		$('#'+file.id).remove(); 
	}else{
		$('#'+file.id).replaceWith(data.html); 
	}
});


	
jQuery(function($){
	$('#droparea').bind({
	   dragover : function(e){
	
	   $(this).addClass('hover'); 
	   },
	   dragleave : function(e){
	
	   $(this).removeClass('hover'); 
	   }
	});
	
	$('.del').live('click',function(e){
	e.preventDefault();
	
	var elem = $(this); 
		if(confirm('Voulez vous vraiment supprimer ce fichier ?')){
		$.get('<?php echo Router::url('mediamanager/rpc'); ?>',{action:'delete',id:elem.attr('href')});
		elem.parent().parent().parent().slideUp(); 
		}
	return false; 
	});
})
	
	
	