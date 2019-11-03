var editor;
function init() {
  // ace editor init
  editor = ace.edit("editor");
  editor.setTheme("ace/theme/monokai");
  editor.session.setMode("ace/mode/php");
  editor.setOptions({
    fontSize: "14pt",
    tabSize: 2,
    useSoftTabs: true
  });
  
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
  
  // load current file
  loadCurrentFile();
  
  // load current dir
  loadCurrentDir();
  
  // load current template path
  loadCurrentTemplatePath();
  
  render();
}

var to = {};
var state = {
  current_file: 'app/routes.php',
  current_dir: 'app/Controller',
  show_file_input: false,
  edited: false,
  files_list: [],
  files_template_list: [],
  mode: 'logic',
  current_template_path: 'templates/default'
};

/**
 *  Render function
 */
function render() {
  // mode: logic/template button
  $('.editor_mode a').removeClass('active');
  $('#button_' + state.mode).addClass('active');   
  
  // mode: template
  if (state.mode == 'template') {
    $('.topbuttons_f').hide();
    $('.sidebar_header').hide();
  } else {
    $('.topbuttons_f').show();
    $('.sidebar_header').show();
  }
  
  // current dir list
  $('ul.listmenu li').off('click');
  var html = "";
  for (let i = 0; i < state.files_list.length; i++) {
    var filepath = state.current_dir + '/' + state.files_list[i];
    html += '<li data-file="' + filepath + '">' + state.files_list[i] + '</li>';
  }
  $('ul.listmenu').html(html);
  $('ul.listmenu li*[data-file="' + state.current_file + '"]').addClass('active');
  $('ul.listmenu li').on('click', function() {
    /*
    var f = $(this).data('file');
    state.current_file = f;
    state.show_file_input = false;
    render();
    load_file();
    */
  });
  
  // current template path list
  $('ul.listmenu_template li').off('click');
  var html = "";
  for (let i = 0; i < state.files_template_list.length; i++) {
    html += '<li>' + state.files_template_list[i] + '</li>';
  }
  $('ul.listmenu_template').html(html);
  $('ul.listmenu_template li*[data-file="' + state.current_file + '"]').addClass('active');
  $('ul.listmenu_template li').on('click', function() {
    /*
    var f = $(this).data('file');
    state.current_file = f;
    state.show_file_input = false;
    render();
    load_file();
    */
  });
}

/**
 *  Utility functions
 */
 
function show_layer(id) {
  if (to[id]) {
    clearTimeout(to[id]);
    delete(to[id]);
  }
  to[id] = setTimeout(function() {
    $('#loading_layer').show();
  }, 500);
}

function hide_layer(id) {
  if (to[id]) {
    clearTimeout(to[id]);
    delete(to[id]);
  }
  $('#loading_layer').hide();
}

/**
 *  Loads current file in editor
 */
function loadCurrentFile() {
  show_layer('loadCurrentFile');
  $.ajax({
    url: 'php/getfile.php',
    type: 'post',
    dataType: 'json',
    data: {
      file: state.current_file
    },
    success: function(json) {
      var content = json.content;
      editor.session.setValue(content);
      state.edited = false;
      hide_layer('loadCurrentFile');
      render();
    }
  });
}
 
/**
 *  Loads current directory in listmenu
 */
function loadCurrentDir() {
  show_layer('loadCurrentDir');
  $.ajax({
    url: 'php/getdir.php',
    type: 'post',
    dataType: 'json',
    data: {
      dir: state.current_dir
    },
    success: function(json) {
      state.files_list = json.content;
      hide_layer('loadCurrentDir');
      render();
    }
  });
}

/**
 *  Loads template directory in listmenu_template
 */
function loadCurrentTemplatePath() {
  show_layer('loadCurrentTemplatePath');
  $.ajax({
    url: 'php/getdir_tpl.php',
    type: 'post',
    dataType: 'json',
    data: {
      dir: state.current_template_path
    },
    success: function(json) {
      state.files_template_list = json.content;
      hide_layer('loadCurrentTemplatePath');
      render();
    }
  });
}

/**
 *  Actions functions
 */
 
// change editor mode
$('.editor_mode a').on('click', function() {
  state.mode = this.id.replace('button_', '');
  render();
});

init();



/*
function render() {
  console.log('render');
  
  // topbutton
  $('.header a').removeClass('active');
  $('.header a*[data-file="' + state.current_file + '"]').addClass('active');
  
  // sidebutton
  $('.sidebar_header button').removeClass('active');
  $('.sidebar_header button*[data-dir="' + state.current_dir + '"]').addClass('active');
  
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



function load_dir() {
  show_layer('load_dir');
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
      hide_layer('load_dir');
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
  $('a[data-file="app/routes.php"]').trigger('click');
  $('button[data-dir="app/Controller"]').trigger('click');
}

init();

*/
