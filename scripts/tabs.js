// last selected Tab
var old_tab = null;
// styles for events
var styles = {
  'over' : ['#000', 'pointer','rgb(255,113,19)'],
  'out'  : ['#222', 'auto','rgb(255,113,19)'],
  'click': ['rgb(255,113,19)', 'default','#222']
};

// handler for Tabs
function selTab( tab, type_select )
{
  // method ignore for select Tab
  if (tab.className != 'sel-tabs')
  {
    with (tab.style)
    {
      if (type_select == 1) // onMouseOver
      {
        // set background
        backgroundColor = styles['over'][0];
        // set cursor type
        cursor = styles['over'][1];
        // set color
        color  = styles['over'][2];
      }
      else if (type_select == 2)  // onClick
      {
        if (old_tab)
        {
          // unset of class name
          old_tab.className = '';
          // reconstruction of default style
          selTab( old_tab, 0 );
        }
        // set class name (for the selected Tab)
        tab.className = 'sel-tabs';
        // set background
        backgroundColor = styles['click'][0];
        // set cursor type
        cursor = styles['click'][1];
        // set color
        color  = styles['click'][2];
        // save select tab
        old_tab = tab;
      }
      else // onMouseOut
      {
        // set background
        backgroundColor = styles['out'][0];
        // set cursor type
        cursor = styles['out'][1];
        // set color
        color  = styles['out'][2];
      }
    }
  }
}
                    
// data load
function UpdateTab( tab, request )
{
  // id for update
  var id_name  = 'ajax-tabs-content';
  // show 'loading...'
  var text  = '<img style="position:relative;top:3px;"' +
   'src="http://crossbrowserajax.com/' +
   'data/images/loading_01.gif">Loading...';
  // use cache
  var caching  = true;
  // template
  var template  = '<h3>%header%</h3><div class="code">%code%</div>';
  // Tab select
  selTab( tab, 2 );
  // request
  cbaUpdateElement(
    id_name,
    'http://crossbrowserajax.com/' +
     'data/examples/ajaxtabs.php?tab=' + request,
    text,
    caching,
    template);
}