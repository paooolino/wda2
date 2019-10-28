// ace editor init
var editor = ace.edit("editor");
editor.setTheme("ace/theme/monokai");
editor.session.setMode("ace/mode/php");
editor.setOptions({
  fontSize: "14pt",
  tabSize: 2,
  useSoftTabs: true
});
editor.commands.addCommand({
  name: 'save',
  bindKey: {win: "Ctrl-S", "mac": "Cmd-S"},
  exec: save
});

//  ace editor save function
function save() {
  $('#saving').show();
  $.ajax({
    url: 'save.php',
    type: 'post',
    dataType: 'json',
    data: {
      value: editor.session.getValue()
    },
    failure: function() {
      alert('failed');
    },
    success: function(json) {
      if (json.result) {
        $('#saving').hide();
      } else {
        alert('WARNING: save failed.');
      }
    }
  });
}

// topbar buttons
$('.topbuttons_f a').on('click', function() {
  var f = $(this).data('file');
  $('.header a').removeClass('active');
  $(this).addClass('active');
  $.ajax({
    url: 'php/getfile.php',
    type: 'post',
    dataType: 'json',
    data: {
      file: f
    },
    success: function(json) {
      var content = json.content;
      editor.session.setValue(content);
    }
  });
});

$('.topbuttons_d a').on('click', function() {
  var d = $(this).data('dir');
  $('.header a').removeClass('active');
  $(this).addClass('active');
  $.ajax({
    url: 'php/getdir.php',
    type: 'post',
    dataType: 'json',
    data: {
      dir: d
    },
    success: function(json) {
      editor.session.setValue('');
    }
  });
});