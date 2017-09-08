$(document).ready(function(){
    restGET(BASE_URL + "group/list", {
        "Acc-Id"  : localStorage.getItem('ACC_ID'),
        "Acc-Key" : localStorage.getItem('ACC_KEY')
    }, function(status, data){
        console.log(data.responseJSON);
        obj = data.responseJSON;
        $("#name").val(obj.data.name);
        $("#description").val(obj.data.description);
        $("#image").val(obj.data.image);
        $("#creator").val(obj.data.create);
    });
});