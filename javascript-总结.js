/**********************************JAVASCRIPT***************************************/



















/**********************************JQUERY***************************************/

//通过POST上传文件
function upFile(){
	file = $('#file')[0].files[0]; //获取上传文件
	var data = new FormData();
	data.append('file', file);
	$.ajax({
		url: "{:U('Account/impor')}",type: 'POST',data: data,cache: false,
		contentType: false, //不可缺
		processData: false, //不可缺 ->当设置为true的时候,jQuery ajax 提交的时候不会序列化 data，而是直接使用data
		success: function(data) {
			$.each( data_json, function(index, val){ 
			
			});
		}
	});
}