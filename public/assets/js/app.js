function restPost(url, header, data, result)
{
	$.ajax({
		type : "POST",
		url : url,
		data : JSON.stringify(data),
		headers : header,
		contentType : "application/json; charset=utf-8",
		crossDomain : true,
		dataType : "json",
		success : function(data, status, jqXHR) {
			result(status, jqXHR);
		},
		error : function (jqXHR, status) {
			data = {};
			resuit(status, jqXHR);
		}
	});
}

function restGet(url, header, result)
{
	$ajax({
		type : "GET",
		url : url,
		headers : header,
		contentType : "application/json; charset=utf-8",
		crossDomain : true,
		dataType : "json",
		success : function(data, stataus, jqXHR) {
			result(status, jqXHR);
		},
		error : function(jqXHR, status) {
			data = {};
			result(status, jqXHR);
		}
	});
}