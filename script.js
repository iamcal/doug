function get_tr_display(val){
	if (document.all){
		return val ? 'block' : 'none';
	}
	return val ? 'table-row' : 'none';
}

function showattach(){
	document.getElementById('attachlink').style.display = 'none';
	document.getElementById('editrow-attach').style.display = get_tr_display(1);
}

function showfilter(){
	document.getElementById('filterlink').style.display = 'none';
	document.getElementById('filterform').style.display = 'block';
}

