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
  // file corrente evidenziato nella file list (logic, template) o nei top button
  current_file: 'app/routes.php',
  // directory corrente per evidenziare il bottone nel sidebar header
  current_dir: 'app/src/Controller',
  // lazy loaded template subdirs
  template_subdirs: {},
  // others
  show_file_input: false,
  edited: false,
  files_list: [],
  files_template_list: [],
  mode: 'logic',
  // path del templte di default
  current_template_path: 'templates/default'
};

/**
 *  Render function
 */
function render() {
  // mode: template
  if (state.mode == 'template') {
    $('.topbuttons_f').hide();
    $('#sidebar_header').hide();
    $('#listmenu').hide();
    $('#listmenu_template').show();
  } else {
    $('.topbuttons_f').show();
    $('#sidebar_header').show();
    $('#listmenu').show();
    $('#listmenu_template').hide();
  }
  
  // topbutton
  $('#header a').removeClass('active');
  $('#header a*[data-file="' + state.current_file + '"]').addClass('active');
  
  // sidebutton
  $('#sidebar_header button').removeClass('active');
  $('#sidebar_header button*[data-dir="' + state.current_dir + '"]').addClass('active');

  // mode: logic/template button
  $('.editor_mode a').removeClass('active');
  $('#button_' + state.mode).addClass('active');   
  
  // sidebar: file input
  $('.add_input').hide();
  if (state.show_file_input) {
    $('.add_input').show();
    $('.add_input').val('Senza nome');
    $('.add_input').select();
  }
  
  // current dir list
  $('ul#listmenu li').off('click');
  var html = "";
  for (let i = 0; i < state.files_list.length; i++) {
    var filepath = state.current_dir + '/' + state.files_list[i];
    html += '<li data-file="' + filepath + '">' + state.files_list[i] + '</li>';
  }
  $('ul#listmenu').html(html);
  $('ul#listmenu li[data-file="' + state.current_file + '"]').addClass('active');
  $('ul#listmenu li').on('click', function() {
    state.current_file = $(this).data('file');
    state.show_file_input = false;
    render();
    loadCurrentFile();
  });
  
  // current template path list
  $('ul#listmenu_template li').off('click');
  var html = "";
  for (let i = 0; i < state.files_template_list.length; i++) {
    var type = state.files_template_list[i].type;
    var name = state.files_template_list[i].name;
    var data_file = state.files_template_list[i]['data-file'];
    var icon = '';
    if (type == 'dir') icon = '<i class="far fa-folder"></i> ';
    html += '<li data-file="' + data_file + '" data-type="' + type + '">' + icon + name + '</li>';
  
    // look for subdirs
    html += render_subdir_list(data_file);
  }
  $('ul#listmenu_template').html(html);
  $('ul#listmenu_template li[data-file="' + state.current_file + '"]').addClass('active');
  $('ul#listmenu_template li').on('click', function() {
    // may be directory or file
    state.current_file = $(this).data('file');
    state.show_file_input = false;
    render();
    if ($(this).data('type') == 'file') {
      loadCurrentFile();
    }
    if ($(this).data('type') == 'dir') {
      loadCurrentTemplateDir();
    }
  });
  
  // sidebar footer button availability
  if ($('#listmenu_container li.active').is(':visible')) {
    $('.delete_button').prop('disabled', false);
    $('.rename_button').prop('disabled', false);
  } else {
    $('.delete_button').prop('disabled', true);
    $('.rename_button').prop('disabled', true);
  }
}

function render_subdir_list(dir, level) {
  if (!level)
    level = 1;
  
  var html = '';
  if (state.template_subdirs[dir]) {
    for (let i = 0; i < state.template_subdirs[dir].length; i++) {
      var type = state.template_subdirs[dir][i].type;
      var name = state.template_subdirs[dir][i].name;
      var data_file = state.template_subdirs[dir][i]['data-file'];
      var icon = '';
      if (type == 'dir') icon = '<i class="far fa-folder"></i> ';
      html += '<li class="level-' + level + '" data-file="' + data_file + '" data-type="' + type + '">' + icon + name + '</li>';
    
      // look for subdirs
      html += render_subdir_list(data_file, level+1);
    }
  }
  return html;
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
 *  Loads current template subdirectory and attach to the list
 */
function loadCurrentTemplateDir() {
  show_layer('loadCurrentTemplateDir');
  $.ajax({
    url: 'php/getdir_tpl.php',
    type: 'post',
    dataType: 'json',
    data: {
      dir: state.current_file
    },
    success: function(json) {
      state.template_subdirs[state.current_file] = json.content;
      hide_layer('loadCurrentTemplateDir');
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
 *  Adds a new file in the current directory
 */
function add_file() {
  if (state.show_file_input == false) {
    state.show_file_input = true;
    render();
  } else {
    // check name
    // to do
    
    // save
    show_layer('add_file');
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
        hide_layer('add_file');
        loadCurrentDir();
      }      
    });
  }
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

/**
 *  Action: click on topbar (file) button
 */
$('.topbuttons_f a').on('click', function() {
  state.current_file = $(this).data('file');
  state.show_file_input = false;
  render();
  loadCurrentFile();
});

/**
 *  Action: click on "add" file button
 */
$('button.add_button').on('click', function() {
  add_file();
});
$('.add_input').on('keypress', function(e) {
  if (e.which == 13) {
    add_file();
  }
});

/**
 *  Action: click on sidebar header directory buttons
 */
$('#sidebar_header button').on('click', function() {
  state.current_dir = $(this).data('dir');
  state.show_file_input = false;
  render();
  loadCurrentDir();
});

/*
function render() {
  console.log('render');
    

  
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













function init() {
  $('a[data-file="app/routes.php"]').trigger('click');
  $('button[data-dir="app/Controller"]').trigger('click');
}

init();

*/
