var rsMedia={url:"index.php?option=com_redshop&view=media&task=ajaxUpload",delUrl:"index.php?option=com_redshop&view=media&task=ajaxDelete",dropzoneFromHtml:$("#j-dropzone-form").html(),dropzoneInstance:{},galleryItems:[],cropping:function(e){$(document).on("click","button.cropping",function(a){a.preventDefault();var t=e.files[0];if(!t)return $("#alertModal").find(".alert-text").text("Please insert an image!!!"),void $("#alertModal").modal("show");if(!(t.width<100)){var n=t.name;console.log(t);var i=$("#cropModal"),o=i.find(".crop-upload"),d=$("<img />"),l=new FileReader;l.onloadend=function(){d.attr("src",l.result),i.find(".image-container").html(d),d.cropper({dragMode:"move",autoCropArea:.5,movable:!1,cropBoxResizable:!0,minContainerWidth:320,minContainerHeight:320,viewMode:3,zoomable:!1})},t.preload?l.readAsDataURL(rsMedia.dataURItoBlob(t.blob)):l.readAsDataURL(t),o.off("click"),i.modal("show"),o.on("click",function(){var a=d.cropper("getCroppedCanvas").toDataURL(),o=rsMedia.dataURItoBlob(a);o.cropped=!0,o.name=n,console.log(n),console.log(o),e.removeFile(t),e.addFile(o),i.modal("hide")})}})},dropzone:function(){if(Dropzone.autoDiscover=!1,$("body").append(this.dropzoneFromHtml),$("#j-dropzone").length){var e=new Dropzone("#j-dropzone",{url:rsMedia.url,autoProcessQueue:!1,thumbnailWidth:null,thumbnailHeight:null,previewTemplate:$("#j-dropzone-tpl").html()});this.dropzoneInstance=e,this.dropzoneEvents(this.dropzoneInstance),this.cropping(this.dropzoneInstance)}},dropzoneEvents:function(e){e.on("addedfile",function(e){return e.type.indexOf("image/")<0?(this.removeFile(e),$("#alertModal").find(".alert-text").text("You can not upload this type of file!"),void $("#alertModal").modal("show")):void(this.files.length>1&&this.removeFile(this.files[0]))}),e.on("success",function(e,a){a=JSON.parse(a),a.success&&$(".img-select").val(a.data.file.url)}),$(document).on("click","button.removing",function(a){if(e.removeAllFiles(),$(".img-select").val(""),$("#image_delete").length<=0){var t=$("<input/>");t.attr("id","image_delete").attr("name","image_delete").attr("type","hidden").val(!0),$("#adminForm").append(t[0])}})},dropzonePreload:function(e,a){a&&(newfile=rsMedia.dataURItoBlob(a.blob),newfile.name=a.name,e.emit("thumbnail",a,a.url),e.addFile(newfile))},dataURItoBlob:function(e){for(var a=atob(e.split(",")[1]),t=new ArrayBuffer(a.length),n=new Uint8Array(t),i=0;i<a.length;i++)n[i]=a.charCodeAt(i);return new Blob([t],{type:"image/jpg"})},init:function(){$.fn.modalmanager.defaults.backdropLimit=1,this.dropzone(),this.galleryEvents()},galleryDropzone:function(){if($("#g-dropzone").length){var e=new Dropzone("#g-dropzone",{url:rsMedia.url,maxFiles:1,thumbnailWidth:null,thumbnailHeight:null,previewTemplate:$("#g-dropzone-tpl").html()});this.galleryDropzoneEvents(e)}},galleryEvents:function(){$(".choosing").on("click",function(e){e.preventDefault(),$("#galleryModal").modal("show")}),$(document).on("click",".img-obj",function(e){e.preventDefault(),$(this).hasClass("selected")?$(this).removeClass("selected"):($(".img-obj").removeClass("selected"),$(this).addClass("selected"),rsMedia.showInfoThumbnail(this)),rsMedia.resetInfoThumbnail(),rsMedia.toggleInsert()}),$("#type-filter").on("change",function(e){var a=$(this).val();return"all"==a?void $(".img-obj").parent().removeClass("hidden"):"attached"==a?void $(".img-obj > img:not([data-attached=true])").parent().parent().addClass("hidden"):($(".img-obj > img:not([data-media="+a+"])").parent().parent().addClass("hidden"),void $(".img-obj > img[data-media="+a+"]").parent().parent().removeClass("hidden"))}),$(".btn-insert").on("click",function(e){e.preventDefault();var a=$(".img-obj.selected").find("img").first(),t=a.attr("src");$(".img-select").val(t);var n=new XMLHttpRequest;n.open("GET",t),n.responseType="blob",n.send(),n.addEventListener("load",function(){var e=new FileReader;e.readAsDataURL(n.response),e.addEventListener("loadend",function(){var t=rsMedia.dataURItoBlob(e.result);t.name=a.attr("alt"),rsMedia.dropzoneInstance.addFile(t),$("#galleryModal").modal("hide")})})}),$(".btn-del-g").on("click",function(e){e.preventDefault(),$("#alertGModal").find(".btn-confirm-del-g").data("id",$(this).data("id")),$("#alertGModal").modal("show")}),$(".btn-confirm-del-g").on("click",function(e){e.preventDefault();var a=$(this).data("id");a&&$.ajax({url:rsMedia.delUrl,method:"post",data:{id:a}}).done(function(e){$("#galleryModal").find(".img-obj.selected").parent().remove(),$(".pv-wrapper").addClass("hidden")}).always(function(e){$("#alertGModal").modal("hide")})})},galleryDropzoneEvents:function(e){e.on("sending",function(e,a,t){t.append("new",!0)}),e.on("success",function(a,t){t=JSON.parse(t);var a=t.data.file,n=$("#g-item-tpl").html(),i=$(n),o={};"image"==a.mime?(i.find("span.img-type").remove(),o=i.find("img.img-type")):(i.find("img.img-type").remove(),o=i.find("span.img-type")),o.attr("src","/"+a.url),o.attr("alt",a.name),o.data("id",a.id),o.data("size",a.size),o.data("dimension",a.dimension),o.data("media",a.media),i.find(".img-mime").data("mime",a.mime),""!=a.mime&&(o.find("i.fa").removeClass("fa-file-o").addClass("fa-file-"+a.mime+"-o"),i.find(".img-mime i.fa").removeClass("fa-file-o").addClass("fa-file-"+a.mime+"-o")),i.find(".img-name").text(a.name),$("#upload-lib .list-pane").append(i[0]),$('#g-tab a[href="#upload-lib"]').tab("show"),e.removeAllFiles()})},showInfoThumbnail:function(e){var a=$(e).find(".img-type"),t={id:a.data("id"),url:a.attr("src"),name:a.attr("alt"),size:a.data("size"),dimension:a.data("dimension")};$img=a.clone();var n=$(".preview-pane");n.find(".pv-img .img-type").remove(),n.find(".pv-img").append($img),n.find(".pv-zoom").attr("href",t.url),n.find(".pv-zoom").attr("data-title",t.name),n.find(".pv-link").attr("href",t.url),n.find(".pv-name").text(t.name),n.find(".pv-size").text(t.size),n.find(".pv-dimension").text(t.dimension),n.find(".pv-url").html('<input type="text" value="'+t.url+'" class="form-control" readonly="true">'),n.find(".pv-remove > a").data("id",t.id),n.find(".pv-wrapper").removeClass("hidden")},resetInfoThumbnail:function(){var e=$(".preview-pane");$(".img-obj.selected").length<=0&&e.find(".pv-wrapper").addClass("hidden")},toggleInsert:function(){$(".img-obj.selected").length>0?$(".btn-insert").removeAttr("disabled"):$(".btn-insert").attr("disabled","true")}};