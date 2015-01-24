var Test = {
	
	print: function() {
		console.log("hello world is awesome");
	},
	
	bar: function() {
		console.log("baz");
		Dispatcher.emitEvent("test");
	}
	
}