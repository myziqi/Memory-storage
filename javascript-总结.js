/**********************************JAVASCRIPT***************************************/



















/**********************************JQUERY***************************************/

//ͨ��POST�ϴ��ļ�
function upFile(){
	file = $('#file')[0].files[0]; //��ȡ�ϴ��ļ�
	var data = new FormData();
	data.append('file', file);
	$.ajax({
		url: "{:U('Account/impor')}",type: 'POST',data: data,cache: false,
		contentType: false, //����ȱ
		processData: false, //����ȱ ->������Ϊtrue��ʱ��,jQuery ajax �ύ��ʱ�򲻻����л� data������ֱ��ʹ��data
		success: function(data) {
			$.each( data_json, function(index, val){ 
			
			});
		}
	});
}