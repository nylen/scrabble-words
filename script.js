/* This file is part of Scrabble-Words.
 * Copyright (C) 2011 by James Nylen.
 *
 * Scrabble-Words is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Scrabble-Words is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Scrabble-Words.  If not, see <http://www.gnu.org/licenses/>.
 */

var sortCustomDate = fdTableSort.sortNumeric;

function sortCustomDatePrepareData(tdNode, innerText) {
  return parseInt($(tdNode).data('timestamp'));
}

function sortCompleteCallback() {
  scroll(0, 0);
}

$(function() {
  var jumpToWordDefaultText = 'Jump to word';
  var jumpToWordTimeout = 300;
  var jumpToWordAnimateLength = 250;

  var $jumpToWord = $('#jump-to-word');
  var jumpToWordTimeoutID = 0;
  var jumpToWordFocused = false;

  function jumpToWord() {
    var sel = '#word-' + $jumpToWord.val();
    if($(sel).length) {
      $('body').scrollTo(sel, jumpToWordAnimateLength, {
        offset: -38
      });
      if(jumpToWordFocused) {
        $jumpToWord[0].select();
      }
    }
  }

  $jumpToWord.blur(function() {
    jumpToWordFocused = false;
    if($(this).val() == '' || $(this).val() == jumpToWordDefaultText) {
      $(this).val(jumpToWordDefaultText).addClass('inactive');
    }
  }).focus(function() {
    this.select();
    jumpToWordFocused = true;
    if($(this).val() == '' || $(this).val() == jumpToWordDefaultText) {
      $(this).val('').removeClass('inactive');
    }
  }).keyup(function(e) {
    if(e.keyCode >= 65 && e.keyCode <= 90) { // letters only
      var val = $(this).val();
      if(val && val != jumpToWordDefaultText && /^[a-z]+$/.test(val)) {
        clearTimeout(jumpToWordTimeoutID);
        jumpToWordTimeoutID = setTimeout(jumpToWord, jumpToWordTimeout);
      }
    }
  }).keypress(function(e) {
    return (e.keyCode != 13); // disable form submission by Enter here
  }).trigger('blur');

  $('#jump-to-top').click(function() {
    $('body').scrollTo(0, jumpToWordAnimateLength);
    $jumpToWord.val('')[0].focus();
    return false;
  });

  $('#add-words-text')[0].focus();
});
