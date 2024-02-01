jQuery(document).ready(function ($) {
  var widget = $(fsWidgetParams.widgetSelector);
  if (widget.length === 0) return; // Exit if widget is not found

  var footer = $(fsWidgetParams.footerSelector);
  var footerExists = footer.length > 0;

  var enableOnMobile = fsWidgetParams.enableMobile === "1";
  var isMobile = window.matchMedia("(max-width: 768px)").matches;
  if (isMobile && !enableOnMobile) return;

  var widgetOffset = widget.offset().top;
  var topPadding = parseInt(fsWidgetParams.topPadding) || 0;
  var gap = parseInt(fsWidgetParams.gap) || 0;
  var widgetOriginalWidth = widget.width();

  $(window).scroll(function () {
    var scrollPosition = $(window).scrollTop();
    var footerOffset = footerExists ? footer.offset().top : Infinity; // Use Infinity if footer does not exist
    var widgetHeight = widget.outerHeight();
    var widgetBottomPosition = scrollPosition + topPadding + widgetHeight;

    if (scrollPosition + topPadding > widgetOffset) {
      if (widgetBottomPosition + gap <= footerOffset) {
        widget.css({
          position: "fixed",
          top: topPadding + "px",
          width: widgetOriginalWidth,
        });
      } else if (footerExists) {
        var distanceFromFooterTop = widgetBottomPosition + gap - footerOffset;
        widget.css({
          position: "fixed",
          top: topPadding - distanceFromFooterTop + "px",
          width: widgetOriginalWidth,
        });
      }
    } else {
      widget.css({
        position: "relative",
        top: "",
        width: "auto",
      });
    }
  });
});
