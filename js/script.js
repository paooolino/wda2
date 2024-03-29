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
          loadCurrentFile({replace_content:false});
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
    state.edited = !editor.session.getUndoManager().isClean();
    render();
  });
  
  // jstree
  $('#jstree_container')
    .on('select_node.jstree', function (e, data) {
      var item = data.instance.get_node(data.selected[0]).original;
      if (item.type == 'dir') {
        state.show_file_input = false;
        state.show_file_input_rename = false;
        render();
      }
      if (item.type == 'file') {
        state.current_file = data.selected[0];
        state.show_file_input = false;
        state.show_file_input_rename = false;
        render();
        loadCurrentFile();
      }
    })
    .jstree({
      'core' : {
        'data' : {
          'url' : 'php/getDirForTree.php',
          'dataType': 'json',
          'data' : function (node) {
            return { 'id' : node.id };
          }
        }
      }
    });
  
  // load current file
  loadCurrentFile();
  
  // load current dir
  loadCurrentDir();
  
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
  // editor bar dependencies of the current loaded file
  file_deps: [],
  // notices
  notices: [],
  // others
  show_file_input: false,
  show_file_input_rename: false,
  edited: false,
  files_list: [],
  files_template_list: [],
  mode: 'logic',
  // path del templte di default
  current_template_path: 'templates/default'
};
function setState(obj) {
  // to do: set state and save in session
  // every state set should use this function
  // to do: load state and set (in init)
}

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
    if ($('#listmenu_template li').length > 0) {
      $('button.init_tpl').hide();
    } else {
      $('button.init_tpl').show();
    }
  } else {
    $('.topbuttons_f').show();
    $('#sidebar_header').show();
    $('#listmenu').show();
    $('#listmenu_template').hide();
    $('button.init_tpl').hide();
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
  // for rename
  if (state.show_file_input_rename) {
    $('.add_input').show();
    if (state.mode == 'template')
      $('.add_input').val(getSelectedTreeNode().text);
    else if(state.mode == 'logic')
      $('.add_input').val(state.current_file.split('/').slice(-1));
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
    state.show_file_input_rename = false;
    render();
    loadCurrentFile();
  });
  
  // sidebar footer button availability
  var node = getSelectedTreeNode();
  if (
    (state.mode == 'logic' && $('#listmenu_container li.active').is(':visible'))
    || (state.mode == 'template' && node && node.type == 'file') 
  ) {
    // bottoni abilitati
    $('.delete_button').prop('disabled', false);
    $('.rename_button').prop('disabled', false);
  } else {
    // bottoni disabilitati
    $('.delete_button').prop('disabled', true);
    $('.rename_button').prop('disabled', true);
  }
  
  // editor bar
  var appendix = '';
  if (state.edited === true)
    appendix = '*';
  var lines = [];
  lines.push('<div>[' + state.current_file + ']' + appendix + '</div>');
  if (state.file_deps.length > 0) {
    var deps_html = 'Deps: ';
    for (var i = 0; i < state.file_deps.length; i++) {
      var dep_name = state.file_deps[i].name;
      deps_html += '<span class="bc">' + dep_name + '</span>';
    }
    lines.push('<div>' + deps_html + '</div>');
  }
  $('#editor_bar').html(lines.join(''));
  
  // notices
  $('#editor_notices .bcbutton').off('click');
  var html = '';
  for (var i = 0; i < state.notices.length; i++) {
    var notice = state.notices[i];
    if (notice.type == 'missing_controller') {
      html += '<div class="notice"><i class="fas fa-exclamation-triangle"></i> controller mancante [' + notice.name + '] <span data-name="' + notice.name + '" data-type="' + notice.type + '" class="bc bcbutton">Crealo</span></div>';
    }
    if (notice.type == 'missing_deps_entry') {
      html += '<div class="notice"><i class="fas fa-exclamation-triangle"></i> manca la definizione del container per questa classe in dependencies.php</div>';
    }
    if (notice.type == 'missing_controller_deps') {
      html += '<div class="notice"><i class="fas fa-exclamation-triangle"></i> manca la definizione del container per il controller: <b>' + notice.name + '</b>. <span data-index="' + i + '" data-type="' + notice.type + '" class="bc bcbutton">incollalo nell\'editor</span></div>';
    }
  }
  $('#editor_notices').html(html);
  $('#editor_notices .bcbutton').on('click', function() {
    if ($(this).data('type') == 'missing_controller') {
      createController($(this).data('name'));
    }
    if ($(this).data('type') == 'missing_controller_deps') {
      var txt = state.notices[$(this).data('index')].code;
      editor.session.insert(editor.getCursorPosition(), txt);
      editor.focus();
    }
  });
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

function getSelectedTreeNode() {
  return $('#jstree_container').jstree('get_node', $('#jstree_container').jstree('get_selected')).original;
}

/**
 *  Loads current file in editor
 */
function loadCurrentFile(opts) {
  show_layer('loadCurrentFile');
  $.ajax({
    url: 'php/getfile.php',
    type: 'post',
    dataType: 'json',
    data: {
      file: state.current_file
    },
    success: function(json) {
      if (!(opts && opts.replace_content == false)) {
        var content = json.content;
        editor.session.setValue(content);
      }
      state.edited = false;
      state.file_deps = json.deps || [];
      state.notices = json.notices;
      hide_layer('loadCurrentFile');
      render();
      //editor.resize();
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
 *  Adds a new file in the current directory
 */
function add_file() {
  if (state.show_file_input == false) {
    state.show_file_input = true;
    render();
  } else {
    // check name
    // to do
    
    var dir = state.current_dir;
    if (state.mode == "template") {
      var dir = 'templates/default/';
      var item = getSelectedTreeNode();
      if (item) {
        if (item.type == 'dir')
          dir = item.id;
        if (item.type == 'file')
          dir = item.id.split('/').slice(0, -1).join('/');
      }
    }
    
    // save
    show_layer('add_file');
    $.ajax({
      url: 'php/add.php',
      type: 'post',
      dataType: 'json',
      data: {
        dir: dir,
        mode: state.mode,
        file: $('.add_input').val()
      },
      success: function(json) {
        state.show_file_input = false;
        state.show_file_input_rename = false;
        hide_layer('add_file');
        if (state.mode == "logic")
          loadCurrentDir();
        if (state.mode == "template")
          $('#jstree_container').jstree().refresh();
      }      
    });
  }
}

/**
 *  Renames the current selected file
 */
function rename_file() {
  if (state.show_file_input_rename == false) {
    state.show_file_input_rename = true;
    render();
  } else {
    var file;
    var newname;
    
    if (state.mode == "template") {
      file = getSelectedTreeNode().id;
      newname = $('.add_input').val();
    }
    if (state.mode == "logic") {
      file = state.current_file;
      newname = $('.add_input').val();
    }
    
    // rename
    show_layer('rename_file');
    $.ajax({
      url: 'php/rename.php',
      type: 'post',
      dataType: 'json',
      data: {
        file: file,
        newname: newname
      },
      success: function(json) {
        state.show_file_input = false;
        state.show_file_input_rename = false;
        hide_layer('rename_file');
        if (state.mode == "logic")
          loadCurrentDir();
        if (state.mode == "template")
          $('#jstree_container').jstree().refresh();
      }      
    });
  }
}

function delete_file() {
  show_layer('delete_file');
  $.ajax({
    url: 'php/delete.php',
    type: 'post',
    dataType: 'json',
    data: {
      file: state.current_file
    },
    success: function(json) {
      hide_layer('delete_file');
      state.current_file = 'app/routes.php';
      if (state.mode == "logic")
        loadCurrentDir();
      if (state.mode == "template")
        $('#jstree_container').jstree().refresh();
      
      loadCurrentFile();
    }      
  });
}

function initialize_template() {
  show_layer('init_tpl');
  $.ajax({
    url: 'php/init_tpl.php',
    type: 'get',
    dataType: 'json',
    success: function(json) {
      hide_layer('init_tpl');
      $('#jstree_container').jstree().refresh();
      render();
    }      
  });
}

function createController(name) {
  show_layer('createController');
  $.ajax({
    url: 'php/create_controller.php',
    type: 'post',
    data: {
      name: name
    },
    dataType: 'json',
    success: function(json) {
      state.current_dir = 'app/src/Controller';
      state.current_file = state.current_dir + '/' + name + '.php';
      hide_layer('createController');
      loadCurrentDir();
      loadCurrentFile();
    }      
  });
}

/**
 *  Actions functions
 */
 
// change editor mode
$('.editor_mode a').on('click', function() {
  state.mode = this.id.replace('button_', '');
  state.show_file_input = false;
  state.show_file_input_rename = false;
  render();
});

init();

/**
 *  Action: click on topbar (file) button
 */
$('.topbuttons_f a').on('click', function() {
  state.current_file = $(this).data('file');
  state.show_file_input = false;
  state.show_file_input_rename = false;
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
    if (state.show_file_input)
      add_file();
    else if (state.show_file_input_rename)
      rename_file();
  }
});

/**
 *  Action: click on sidebar header directory buttons
 */
$('#sidebar_header button').on('click', function() {
  state.current_dir = $(this).data('dir');
  state.show_file_input = false;
  state.show_file_input_rename = false;
  render();
  loadCurrentDir();
});

/**
 *  Action: click "delete" file button
 */
$('.delete_button').on('click', function() {
  if (confirm('Stai per eliminare il file. Confermi?')) {
    delete_file();
  }
});

/**
 *  Action: click "rename" file button
 */
$('.rename_button').on('click', function() {
  rename_file();
});

/**
 *  Action: click "add_route" file button
 */
$('.add_route').on('click', function() {
  var name = prompt("Inserisci il nome della route:");
  if (name && name != '') {
    var route_name = name.replace(/ /g, "_").toUpperCase();
    var words = route_name.split('_');
    words = words.map(function(item, index) {
      var s = item.toLowerCase();
      return s.charAt(0).toUpperCase() + s.slice(1);
    });
    var class_name = words.join('');
    var txt = "$app->get('/', 'WebApp\\Controller\\" + class_name + "C')->setName('" + route_name + "');\r\n";
    editor.session.insert(editor.getCursorPosition(), txt);
    editor.focus();
  }
});

/**
 *  Action: initialize template button
 */
$('button.init_tpl').on('click', function() {
  initialize_template();
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
