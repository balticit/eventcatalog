$(document).ready(function() {
	$('#form_find_artist #mySelect1').change(function() {
		location.href='/artist/?artist_doc_country=' + $(this).val();
	});
});

function FindStylesDlg()
{
  $('#form_find_artist').find('input[name^="artist_doc_style"]').remove();
  Boxy.load('/ajax/atrist_styles',{
      afterShow: function() { FillChecks();},
      title: "Выберите стили",
      closeText: "[X]",
      modal: true,
      clickToFront: true,
      unloadOnHide: true});
}
