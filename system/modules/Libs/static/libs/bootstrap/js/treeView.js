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
        tree.find('li').each(function () {
          var stick = $(this);
        });
        tree.find('li').has("ul").each(function () {
          var branch = $(this); //li with children ul
          branch.prepend("<i class='tree-indicator glyphicon glyphicon-chevron-right'></i>");
          branch.addClass('tree-branch');
          branch.on('click', function (e) {
            if (this == e.target) {
              var icon = $(this).children('i:first');
              icon.toggleClass("glyphicon-chevron-down glyphicon-chevron-right");
              $(this).children().children().toggle();
            }
          })
          if (branch.find('li.active').length || $(this).hasClass('active')) {
            branch.click();
          }
          branch.children().children().toggle();

          branch.children('.tree-indicator, button').click(function (e) {
            branch.click();
            e.preventDefault();
          });
        });
        tree.find('li').not(":has(ul)").each(function () {
          $(this).prepend("<i class='tree-indicator glyphicon glyphicon-minus'></i>");
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