(function(a){a.jqplot.CanvasAxisLabelRenderer=function(b){this.angle=0;this.axis;this.show=true;this.showLabel=true;this.label="";this.fontFamily='"Trebuchet MS", Arial, Helvetica, sans-serif';this.fontSize="11pt";this.fontWeight="normal";this.fontStretch=1;this.textColor="#666666";this.enableFontSupport=true;this.pt2px=null;this._elem;this._ctx;this._plotWidth;this._plotHeight;this._plotDimensions={height:null,width:null};a.extend(true,this,b);if(b.angle==null&&this.axis!="xaxis"&&this.axis!="x2axis"){this.angle=-90}var c={fontSize:this.fontSize,fontWeight:this.fontWeight,fontStretch:this.fontStretch,fillStyle:this.textColor,angle:this.getAngleRad(),fontFamily:this.fontFamily};if(this.pt2px){c.pt2px=this.pt2px}if(this.enableFontSupport){function d(){return !!(document.createElement("canvas").getContext&&typeof document.createElement("canvas").getContext("2d").fillText=="function")}if(d()){this._textRenderer=new a.jqplot.CanvasFontRenderer(c)}else{this._textRenderer=new a.jqplot.CanvasTextRenderer(c)}}else{this._textRenderer=new a.jqplot.CanvasTextRenderer(c)}};a.jqplot.CanvasAxisLabelRenderer.prototype.init=function(b){a.extend(true,this,b);this._textRenderer.init({fontSize:this.fontSize,fontWeight:this.fontWeight,fontStretch:this.fontStretch,fillStyle:this.textColor,angle:this.getAngleRad(),fontFamily:this.fontFamily})};a.jqplot.CanvasAxisLabelRenderer.prototype.getWidth=function(d){if(this._elem){return this._elem.outerWidth(true)}else{var f=this._textRenderer;var c=f.getWidth(d);var e=f.getHeight(d);var b=Math.abs(Math.sin(f.angle)*e)+Math.abs(Math.cos(f.angle)*c);return b}};a.jqplot.CanvasAxisLabelRenderer.prototype.getHeight=function(d){if(this._elem){return this._elem.outerHeight(true)}else{var f=this._textRenderer;var c=f.getWidth(d);var e=f.getHeight(d);var b=Math.abs(Math.cos(f.angle)*e)+Math.abs(Math.sin(f.angle)*c);return b}};a.jqplot.CanvasAxisLabelRenderer.prototype.getAngleRad=function(){var b=this.angle*Math.PI/180;return b};a.jqplot.CanvasAxisLabelRenderer.prototype.draw=function(c){var e=document.createElement("canvas");this._textRenderer.setText(this.label,c);var b=this.getWidth(c);var d=this.getHeight(c);e.width=b;e.height=d;e.style.width=b;e.style.height=d;e.style.position="absolute";this._domelem=e;this._elem=a(e);this._elem.addClass("jqplot-"+this.axis+"-label");e=null;return this._elem};a.jqplot.CanvasAxisLabelRenderer.prototype.pack=function(){if(a.jqplot.use_excanvas){window.G_vmlCanvasManager.init_(document);this._domelem=window.G_vmlCanvasManager.initElement(this._domelem)}this._textRenderer.draw(this._elem.get(0).getContext("2d"),this.label)}})(jQuery);