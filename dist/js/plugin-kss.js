var PluginKSS = {
	
	insert: function() {
		
		console.log("trying to insert");
		
		var name = "pattern-lab-plugin-kss-templates-code-insert";
		
		var template         = document.getElementById(name+"-template");
		var templateCompiled = Hogan.compile(template.innerHTML);
		var templateRendered = templateCompiled.render(patternData.extraOutput);
		
		var p       = document.getElementById('sg-code-extra-output');
		var n       = document.createElement('div');
		n.id        = name;
		n.innerHTML = templateRendered;
		p.parentNode.insertBefore(n, p.nextSibling);
		
	}
	
}