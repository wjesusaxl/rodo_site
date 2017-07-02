(function($) {

	$(document).ready(function() {
	
		$(window).bind('scroll', function() {
			$('#nav').fixedNav({ $window: $(this) });
		});
		
		$('a.external').bind('click', function(e) {
			e.preventDefault();
			
			try {
				mpmetrics.track($(this).data('event'));
			} catch(e) {
			
			}
			
			window.location = $(this).attr('href');
		});
		
		$('body').delegate('nav a[href*="#"]:not(.button)', 'click', function(e) {
			e.preventDefault();

			$(this).goToSection();
		});
		
		$('a.switch').bind('click', function(e) {
			e.preventDefault();
			
			$(this).toggleSwitch();
		}).each(function() {
			if ( $(this).siblings('input').val() == 'false' ) {
				$(this).find('span.switchButton')
					.css({
						'left': 0
					});
				$(this).find('.switchOn')
					.css({
						'width': 0,
						'left': -40
					});
			}
		});
		
		$('#tableBlock').getCommits();
		
		$('#buildTable').bind('click', function(e) {
			e.preventDefault();
			
			try {
				mpmetrics.track('Build-Table-Demo', {
				   'footer': $('#hasFooter').val(),
				   'cloneHeadToFoot': $('#cloneHeadToFoot').val(),
				   'fixedColumn': $('#fixedColumn').val()
				});
			} catch(e) {
			
			}
			
			$('#tableBlock').fixedHeaderTable('destroy')
				.empty()
				.getCommits();
		});
	});
	
	$.fn.extend({
		
		toggleSwitch: function( ) {
			var $self	= $(this),
				self	= this,
				$button = $self.find('span.switchButton'),
				inputId	= $self.data('for'),
				$input	= $('#'+inputId),
				left	= 30;
			
			if ( $button.position().left == 30 ) {
				left = 0;
				$input.val('false');
				$('#demoOptions').text('');
				$self.find('.switchOn')
					.animate({
						'width': '0'
					}, 300, function(){
						$(this).css({ 'left': -40 });
					});
			} else {
				$input.val('true');
				$self.find('.switchOn')
					.css({ 'left': 0 })
					.animate({
						'width': '40',
						'left': 0
					}, 300);
			}

			$('#'+inputId+'Demo').text($input.val());
			
			$button.animate({
					left: left
				}, 200);
		},
		
		fixedNav: function( options ) {
			var $self 			= $(this),
				self  			= this,
				$window 		= options.$window,
				offset			= $self.outerHeight() - $self.find('a').outerHeight() - 26,
				$fixedWrapper 	= $('#fixedNav');

			if ( $window.scrollTop() >= offset && !$('#fixedNav').length ) {
				$fixedWrapper = $('<div id="fixedNav"></div>').prependTo('body');
				$fixedWrapper.append($self.clone())
					.find('nav')
					.removeAttr('id');
					
				setTimeout(function() {$fixedWrapper.find('nav').addClass('fixed');}, 0);
			} else if ( $fixedWrapper.length && $window.scrollTop() <= offset ) {
				$fixedWrapper.remove();
			}
			
			return self;
		},
		
		goToSection: function( options ) {
			var $self 			= $(this),
				self			= this,
				hash  			= $self.attr('href'),
				scrollDistance 	= 0;
				
			hash = hash.replace('#','');
			
			try {
				mpmetrics.track(hash); // track event
			} catch(e) {
			
			}
			
			if ( hash.toLowerCase() != 'home' ) {
				scrollDistance = $('#'+hash).offset().top - ( $('#nav li a').outerHeight(true) + 26 );
			}
			
			$('html,body').animate({
				'scrollTop': scrollDistance
			}, 700);
		},
		
		buildTable: function( options ) {
			var $self 		= $(this),
				self  		= this,
				$footer		= $('#hasFooter'),
				$clone		= $('#cloneHeadToFoot'),
				$fixedColumn = $('#fixedColumn'),
				hasFooter	= ( $footer.val() == 'true' ) ? true : false,
				hasClone	= ( $clone.val() == 'true' ) ? true : false,
				hasFixedColumn = ( $fixedColumn.val() == 'true' ) ? true : false,
				aMessages 	= options.data,
				colSpan = 4,
				$thead,
				$tbody,
				$tfoot,
				$currentRow,
				commitDate,
				authoredDate,
				aCommitMessage,
				commitMessage;
			
			if ( hasFixedColumn == true ) {
				colSpan = 5;
			}
			
			if ( hasFooter && !hasClone ) {
				$self.append('<table class="myTable"><thead></thead><tfoot></tfoot><tbody></tbody></table>')
					.find('tfoot')
					.append('<tr></tr>')
					.find('tr')
					.append('<td colspan="'+colSpan+'">Showing latest commit messages for Fixed-Header-Table master branch</td>');
			} else {
				$self.append('<table class="myTable"><thead></thead><tbody></tbody></table>');
			}
			
			$thead = $self.find('thead');
			$thead.append('<tr></tr>');
			
			$thead.find('tr')
				.append('<th>Committer</th>')
				.append('<th class="date">Commit Date</th>')
				.append('<th>Commit Message</th>')
				.append('<th>Details</th>');
			
			if ( hasFixedColumn == true ) {
				$thead.find('tr')
					.append('<th>Author</th>')
					.append('<th class="date">Authored Date</th>');
			}			

			$tbody = $self.find('tbody');
				
			for ( var message in aMessages ) {
				commitDate = getDateToString(aMessages[message].commit.committer.date);
				authoredDate = getDateToString(aMessages[message].commit.author.date);
				aCommitMessage = aMessages[message].commit.message.split(/\n/);
				commitMessage = "";
				
				for ( var theMessage in aCommitMessage ) {
					if ( aCommitMessage[theMessage] != "" ) {
						commitMessage += "<p>";
						commitMessage += aCommitMessage[theMessage];
						commitMessage += "</p>";
					}
				}

				$currentRow = $('<tr></tr>').appendTo($tbody)
					.append('<td>' + aMessages[message].commit.committer.name + '</td>')
					.append('<td class="textAlignCenter">' + commitDate + '</td>')
					.append('<td>' + commitMessage + '</td>')
					.append('<td class="link"><a class="button" href="http://github.com' + aMessages[message].commit.url + '">View Details</a></td>');
					
				if ( hasFixedColumn == true ) {
					$currentRow.append('<td>'+aMessages[message].commit.committer.name+'</td>')
						.append('<td class="textAlignCenter">'+authoredDate+'</td>');
				}
			}

			$('#tableBlock > table.myTable').fixedHeaderTable({ height: '600', altClass: 'odd', footer: hasFooter, cloneHeadToFoot: hasClone, fixedColumn: hasFixedColumn, themeClass: 'fancyTable' });
			
			return self;
		},

		getCommits: function() {
			var $self   	= $(this),
				self		= this,
				url			= 'https://api.github.com/repos/markmalek/Fixed-Header-Table/commits?callback=?',
				aMessages	= new Array();

			if ( $.isEmptyObject($self.data()) ) {
				$.getJSON(url, function(data) {
					$self.data(data.data);
					$self.buildTable({ data: data.data });
				});
			} else {
				$self.buildTable({ data: $self.data() });
			}
			
			return self;
		}
		
	});
	
	function getDateToString( theDate ) {
		var aDate = theDate.split(/[-T:]/),
		    self = new Date(aDate[0], aDate[1], aDate[2], aDate[3], aDate[4], aDate[5], 0),
			day = self.getDate(),
			month = getMonthToString(self.getMonth()),
			year = self.getFullYear(),
			hours = self.getHours(),
			minutes = self.getMinutes();

		return month + ' ' + day + ', ' + year + '<br /> at ' + getTimeToString(hours, minutes); 
	}
	
	function getTimeToString( hours, minutes ) {
		var period = 'AM';
		
		if ( hours >= 12 ) {
			period = 'PM';
		}
		
		if ( hours > 12 ) {
			hours = hours - 12;
		} else if ( hours < 10 && hours > 0 ) {
			hours = '0' + hours;
		} else {
			hours = 12;
		}
		
		if ( minutes < 10 ) {
			minutes = '0' + minutes;
		}
		
		return hours + ':' + minutes + ' ' + period;
	}
	
	function getMonthToString( month ) {
		switch ( month ) {
			case 1:
				return 'January';
			case 2:
				return 'February';
			case 3:
				return 'March';
			case 4:
				return 'April';
			case 5:
				return 'May';
			case 6:
				return 'June';
			case 7:
				return 'July';
			case 8:
				return 'August';
			case 9:
				return 'September';
			case 10:
				return 'October';
			case 11:
				return 'November';
			case 12:
				return 'December';
		}
	}
	
})(jQuery);