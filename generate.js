var fs = require('fs');

var handlebars = require('handlebars');

var spammers = require('./spammers.json');



fs.readdir('./templates', function(err, files){
	if(err) return console.log(err);

	for(var i in files){
		handleFile(files[i]);
	}
});

function handleFile(file){
	fs.readFile('./templates/' + file, function(err, templateFileContent){
		if(err) return console.log(err);

		templateFileContent = templateFileContent.toString();

		var template = handlebars.compile(templateFileContent);

		var result = template({
			spammers: spammers,
		});

		fs.writeFile('./snippets/' + file, result, function(err){
			if(err) return console.log(err);

			console.log(file + ' generated');
		})
	});
}