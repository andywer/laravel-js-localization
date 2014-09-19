(function(){var a;var c={};var e=function(j,h){for(var f in h){var g=h[f];var i=new RegExp(":"+f,"g");j=j.replace(i,g);}return j;};var d=function(f){for(var g in f){if(f.hasOwnProperty(g)){return false;
}}return true;};var b={get:function(i,h,g){var k=a;if(g){k=g;}if(typeof c[k][i]=="undefined"){var f={};for(var l in c[k]){if(l.indexOf(i+".")>-1){f[l]=c[k][l];
}}if(!d(f)){return f;}return i;}var j=c[k][i];if(h){j=e(j,h);}return j;},has:function(f){return typeof c[a][f]!="undefined";},choice:function(h,j,g){if(typeof c[a][h]=="undefined"){return h;
}var i;var f=c[a][h].split("|");if(j==1){i=f[0];}else{i=f[1];}if(g){i=e(i,g);}return i;},setLocale:function(f){a=f;if(!c[f]){throw new Error('No messages defined for locale: "'+f+'". Did you forget to enable it in the configuration?');
}},locale:function(){return a;},addMessages:function(g){for(var f in g){c[f]=g[f];}}};this.Lang=b;this.trans=b.get;this.transChoice=b.choice;})();
