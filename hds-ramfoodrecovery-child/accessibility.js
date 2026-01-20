(function($) {
// Function to add screen reader text after specified selector
    function addScreenReaderText(selector, text) {
		$(selector).after(`<span class="screen-reader-text">${text}</span>`);
	}

  // Setting aria-label attributes for various elements
  enterDom('#goog-gt-votingInputVote', function(el) {
      el.setAttribute('aria-label', 'Source language');
  });
  enterDom('#goog-gt-votingInputSrcText', function(el) {
    el.setAttribute('aria-label', 'Source text');
  });
  enterDom('#goog-gt-votingInputTrgText', function(el) {
    el.setAttribute('aria-label', 'Target text');
  });
  enterDom('#goog-gt-votingInputSrcLang', function(el) {
    el.setAttribute('aria-label', 'Source Language');
  });
  enterDom('#goog-gt-votingInputTrgLang', function(el) {
    el.setAttribute('aria-label', 'Target Language');
  });
  enterDom('.g-recaptcha-response', function(el) {
    el.setAttribute('aria-label', 'Google Recaptcha');
  });

  addScreenReaderText('#back_to_top .eltd_icon_stack', 'Back to top');
  addScreenReaderText('i.fa-facebook', 'Facebook');
  addScreenReaderText('i.fa-instagram', 'Instagram');
  addScreenReaderText('i.fa-youtube', 'YouTube');

})(jQuery);