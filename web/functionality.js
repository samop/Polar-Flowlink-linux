function getZone(value){
        new Ajax.Request('get_cweek_zones.php?previous='+value,
        {
                method:'get',
                onSuccess: function(transport){
                var response = transport.responseText || "no response text";
                var res=eval("response");
		alert(res);
		return res;
                },
                onFailure: function(){ alert('Something went wrong...') }
        });
}


