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
editor.on('input', function(delta) {
  console.log('input');
  state.edited = !editor.session.getUndoManager().isClean();
  render();
});

var to;
var state = {
  current_file: '',
  current_dir: '',
  show_file_input: false,
  edited: false,
  files_list: []
};

function render() {
  console.log('render');
  
  // topbutton
  $('.header a').removeClass('active');
  $('.header a*[data-file="' + state.current_file + '"]').addClass('active');
  
  // sidebutton
  $('.sidebar_header button').removeClass('active');
  $('.sidebar_header button*[data-dir="' + state.current_dir + '"]').addClass('active');

  // sidebar: file list
  $('ul.listmenu li').off('click');
  var html = "";
  for (let i = 0; i < state.files_list.length; i++) {
    var filepath = state.current_dir + '/' + state.files_list[i];
    html += '<li data-file="' + filepath + '">' + state.files_list[i] + '</li>';
  }
  $('ul.listmenu').html(html);
  $('ul.listmenu li*[data-file="' + state.current_file + '"]').addClass('active');
  $('ul.listmenu li').on('click', function() {
    var f = $(this).data('file');
    state.current_file = f;
    state.show_file_input = false;
    render();
    load_file();
  });
  
  // sidebar: file input
  $('.add_input').hide();
  if (state.show_file_input) {
    $('.add_input').show();
    $('.add_input').val('Senza nome');
    $('.add_input').select();
  }
  
  // editor
  if (state.current_file == '') {
    $('#editor').hide();
  } else {
    $('#editor').show();
  }
  
  // editor bar
  if (state.current_file == '') {
    $('.editor_bar').hide();
  } else {
    $('.editor_bar').show();
  }
  var appendix = '';
  if (state.edited === true)
    appendix = '*';
  $('.editor_bar').html('&nbsp;[' + state.current_file + ']' + appendix);
}

function show_layer() {
  to = setTimeout(function() {
    $('#loading_layer').show();
  }, 500);
}

function hide_layer() {
  try {
    clearTimeout(to);
  } catch(err) {};
  $('#loading_layer').hide();
}

function load_dir() {
  show_layer();
  $.ajax({
    url: 'php/getdir.php',
    type: 'post',
    dataType: 'json',
    data: {
      dir: state.current_dir
    },
    success: function(json) {
      state.files_list = json.content;
      render();
      hide_layer();
    }
  });
}

function load_file() {
  show_layer();
  $.ajax({
    url: 'php/getfile.php',
    type: 'post',
    dataType: 'json',
    data: {
      file: state.current_file
    },
    success: function(json) {
      console.log('file loaded');
      
      var content = json.content;
      editor.session.setValue(content);
      state.edited = false;
      render();
      hide_layer();
    }
  });
}

function add_file() {
  if (state.show_file_input == false) {
    state.show_file_input = true;
    render();
  } else {
    // check name
    // to do
    
    // save
    $.ajax({
      url: 'php/add.php',
      type: 'post',
      dataType: 'json',
      data: {
        dir: state.current_dir,
        file: $('.add_input').val()
      },
      success: function(json) {
        state.show_file_input = false;
        load_dir();
      }      
    });
  }
}
  
//  ace editor save function
function save() {
  $('#saving').show();
  $.ajax({
    url: 'php/save.php',
    type: 'post',
    dataType: 'json',
    data: {
      file: state.current_file,
      value: editor.session.getValue()
    },
    failure: function() {
      alert('failed');
    },
    success: function(json) {
      if (json.result) {
        $('#saving').hide();
        state.edited = false;
        render();
      } else {
        alert('WARNING: save failed.');
      }
    }
  });
}

// topbar buttons
$('.topbuttons_f a').on('click', function() {
  var f = $(this).data('file');
  state.current_file = f;
  state.show_file_input = false;
  render();
  load_file();
});

// sidebar directory buttons
$('.sidebar_header button').on('click', function() {
  var d = $(this).data('dir');
  state.current_dir = d;
  state.show_file_input = false;
  render();
  load_dir();
});

// new file button
$('button.add').on('click', function() {
  add_file();
});
$('.add_input').on('keypress', function(e) {
  if (e.which == 13) {
    add_file();
  }
});

function init() {
  $('button[data-dir="app/Controller"]').trigger('click');
}

init();
