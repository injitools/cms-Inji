/**
 *  BootTree Treeview plugin for Bootstrap.
 *
 *  Based on BootSnipp TreeView Example by Sean Wessell
 *  URL:	http://bootsnipp.com/snippets/featured/bootstrap-30-treeview
 *
 *	Revised code by Leo "LeoV117" Myers
 *
 */
inji.onLoad(function () {
  $.fn.extend({
    treeview: function () {
      return this.each(function () {
        // Initialize the top levels;
        var tree = $(this);
        //skip alredy loaded
        if (tree.hasClass('treeview-tree')) {
          return;
        }
        tree.addClass('treeview-tree');
        tree.find('li').has("ul").each(function () {
          var branch = $(this); //li with children ul
          var icon = branch.data('treeview-icon-closed');
          if (!icon) {
            icon = 'glyphicon glyphicon-chevron-right';
          }
          branch.prepend("<i class='tree-indicator " + icon + "'></i>");
          branch.addClass('tree-branch closed');
          branch.on('click', function (e) {
            if (this == e.target) {
              var icon = $(this).children('i:first');
              
              var closedIcon = branch.data('treeview-icon-closed');
              if (!closedIcon) {
                closedIcon = 'glyphicon glyphicon-chevron-right';
              }
              var openedIcon = branch.data('treeview-icon-opened');
              if (!openedIcon) {
                openedIcon = 'glyphicon glyphicon-chevron-down';
              }

              icon.toggleClass(openedIcon + " " + closedIcon);
              $(this).children().children().toggle();
              $(this).toggleClass('opened closed');
            }
          })
          if (branch.find('li.active').length || $(this).hasClass('active')) {
            branch.click();
          }
          branch.children().children().toggle();

          branch.children('.tree-indicator').click(function (e) {
            branch.click();
            e.preventDefault();
          });
        });
        tree.find('li').not(":has(ul)").each(function () {
          var icon = $(this).data('treeview-icon-linear');
          console.log(icon);
          if (!icon) {
            icon = 'glyphicon glyphicon-minus';
          }
          $(this).prepend("<i class='tree-indicator " + icon + "'></i>");
        });
      });
    }
  });

  /**
   *	The following snippet of code automatically converst
   *	any '.treeview' DOM elements into a treeview component.
   */
  $(window).on('load', function () {
    $('.treeview').each(function () {
      var tree = $(this);
      tree.treeview();
    });
  });
});