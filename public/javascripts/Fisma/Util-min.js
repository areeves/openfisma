Fisma.Util={getObjectFromName:function(c){var b=c.split(".");var a=window;for(piece in b){a=a[b[piece]];if(a==undefined){throw"Specified object does not exist: "+c}}return a},positionPanelRelativeToElement:function(b,c){var a=5;b.cfg.setProperty("context",[c,YAHOO.widget.Overlay.TOP_LEFT,YAHOO.widget.Overlay.BOTTOM_LEFT,null,[0,a]])},getTimestamp:function(){var b=new Date();var a=b.getHours()+"";if(a.length==1){a="0"+a}var c=b.getMinutes()+"";if(c.length==1){c="0"+c}var d=b.getSeconds()+"";if(d.length==1){d="0"+d}return a+":"+c+":"+d}};