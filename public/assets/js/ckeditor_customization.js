ClassicEditor
	.create( document.querySelector('#editor_ckeditor'), {
		toolbar : {
			items : [
				'selectAll', 'findAndReplace', 
				'|',
				'undo', 'redo',
				'|',
				'heading',
				'|',
				'fontFamily', 'fontColor','fontBackgroundColor','fontSize','highlight','bold', 'italic', 'strikethrough', 'subscript', 'superscript',
				'|',
				'removeFormat',
				'|',
				'alignment','bulletedList','numberedList',
				'|',
				'link', 'specialCharacters',
				'|',
				'codeBlock','htmlEmbed',
				'|',
				'outdent','indent',
				'|',
				'insertTable','mediaEmbed',
				'|',
				'sourceEditing',
			],
			shouldNotGroupWhenFull: true
		},
		heading: {
			options: [
				{ model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
				// { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
				{ model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
				{ model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
				{ model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
				{ model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
				{ model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
			]
		},
	} )
	.then( editor => {
		window.editor = editor;
	} )
	.catch( handleSampleError );

function handleSampleError( error ) {
	const issueUrl = 'https://github.com/ckeditor/ckeditor5/issues';

	const message = [
		'Oops, something went wrong!',
		`Please, report the following error on ${ issueUrl } with the build id "j8d2q534ha8h-tn6ucpj2aurn" and the error stack trace:`
	].join( '\n' );

	console.error( message );
	console.error( error );
}
