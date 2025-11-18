$(".editBtn").click(function(){
	$("#editMateriaalID").val($(this).data("id"));
	$("#editMateriaalNaam").val($(this).data("naam"));

	let foto = $(this).data("foto");
	if (foto) {
		$("#editFotoPreview").attr("src", foto).removeClass("d-none");
	} else {
		$("#editFotoPreview").addClass("d-none");
	}

	$("#editMateriaalModal").modal("show");
});

$(".deleteBtn").click(function(){
	$("#deleteMateriaalID").val($(this).data("id"));
	$("#deleteMateriaalForm").submit();
});