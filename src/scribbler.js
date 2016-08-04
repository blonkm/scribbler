/* Copyright (c) 2016 Michiel van der Blonk */

(function ( $ ) {
 
    $.fn.scribbler = function( options ) {
 
 			if ($.type(options) === 'string') { 
      	options = {text:options};
      }
      // This is the easiest way to have default options.
      var settings = $.extend({
        fontsize: $(this).height(),
        duration: options.text.length * 400,
        font: 'Tangerine',
        x: 0,
        y: $(this).height()/2,
        text: 'Hello World!',
        kerning: 0.1,
        color: $(this).css('color'),
        brush: 2
      }, options );
 
      function drawpath( canvas, pathstr, duration, attr, callback )
      {
        var guide_path;
        if ( typeof( pathstr ) == "string" )
            guide_path = canvas.path( pathstr ).attr( { stroke: "none", fill: "black", 'fill-opacity':1 } );
        else
            guide_path = pathstr;
        var path = canvas.path( guide_path.getSubpath( 0, 1 ) ).attr( attr );    
        var total_length = guide_path.getTotalLength( guide_path );
        var last_point = guide_path.getPointAtLength( 0 );
        var start_time = new Date().getTime();
        var interval_length = 25;
        var result = path;

        var interval_id = setInterval( function()
        {
            var elapsed_time = new Date().getTime() - start_time;
            var this_length = elapsed_time / duration * total_length;
            var subpathstr = guide_path.getSubpath( 0, this_length );
            attr.path = subpathstr;
            path.animate( attr, interval_length );				
            if ( elapsed_time >= duration )
            {
                clearInterval( interval_id );
                if ( callback != undefined ) callback();
                guide_path.remove();
            }
        }, interval_length );
        return result;
      }
      var paper = new Raphael($(this)[0], $(this).width(), $(this).height());
      var myfont = paper.getFont( settings.font );
      // create an invisible template
      var textGuide = paper.print( 
      	settings.x, settings.y, 
        settings.text, 
        myfont, 
        settings.fontsize, 
        'middle', 
        settings.kerning ).attr( { fill: 'none', stroke: 'none' } );
      // draw (scribble) the text
      var textPath = drawpath( 
      	paper, 
        textGuide, 
        settings.duration, 
        { stroke: settings.color, 
        	fill: settings.color, 
          'fill-opacity': 0, 
          'stroke-width': settings.brush } 
      );    

			return this;
    }; 
}( jQuery ));

jQuery(document).ready(function($){
  var canvas = $('#pen');
  canvas.scribbler(canvas.data('text'));
}); 
