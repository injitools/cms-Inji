/**
 * Ecommerce Classes
 */
inji.Ecommerce = {
  Cart: new function () {
    this.addItem = function (itemOfferPriceId, count, btn, callback) {
      inji.Server.request({
        url: 'ecommerce/cart/add',
        data: {
          itemOfferPriceId: itemOfferPriceId,
          count: count
        },
        success: function (data) {
          if (callback) {
            callback(data, btn);
          }
          inji.Server.request({
            url: 'ecommerce/cart/getCart',
            success: function (data) {
              $("#cart,.cartplace").html(data);
            }
          });
        }
      }, btn);
    };
    this.calcSum = function (form) {
      if (form === undefined) {
        form = $('.ecommerce .cart-order_page form');
      }
      else {
        form = $(form)
      }
      var formData = new FormData(form[0]);
      $('.ecommerce .cart-order_page').prepend($('<div style = "position:absolute;width:' + $('.ecommerce .cart-order_page').width() + 'px;height:' + $('.ecommerce .cart-order_page').height() + 'px;background-color: rgba(255, 255, 255, 0.4);z-index:1000000"></div>'));
      inji.Server.request({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'html',
        processData: false,
        success: function (data) {
          $('.ecommerce .cart-order_page').html($(data).find('.ecommerce .cart-order_page').html());
          if ($(data).find('.alert').length > 0) {
            $.each($(data).find('.alert'), function () {
              //$('.ecommerce .cart-order_page').prepend(this.outerHTML)
            })
          }
        }
      });
    };
    this.delItem = function (cart_item_id, form) {
      $('.cart_item_id' + cart_item_id).remove();
      this.calcSum(form);
    };
    this.delItemWidget = function (cart_item_id, callback) {
      inji.Server.request({
        url: '/ecommerce/cart/deleteItem?cartItemId=' + cart_item_id,
        success: function (data) {
          $("#cart,.cartplace").html(data);
          if (callback !== undefined) {
            callback();
          }
        }
      });
    }
  },
  toggleFav: function (itemId, btn, noChangeText) {
    inji.Server.request({
      url: 'ecommerce/toggleFav/' + itemId,
      success: function (data) {
        $('.ecommerce-favorite-count').html(data.count);
        setTimeout(function () {
          if (!noChangeText) {
            $(btn).html(data.newText);
          }
        }, 100)
      }
    }, btn);
  }
};
inji.onLoad(function () {

  //plugin bootstrap minus and plus
  //http://jsfiddle.net/laelitenetwork/puJ6G/
  $('body').on('click', '.btn-number', function (e) {
    e.preventDefault();

    var fieldName = $(this).data('field');
    var type = $(this).data('type');
    var input = $("input[name='" + fieldName + "']");
    var currentVal = parseFloat(input.val());
    if (!isNaN(currentVal)) {
      if (type == 'minus') {

        if (currentVal > input.attr('min')) {
          input.val(currentVal - 1).change();
        }
        if (parseFloat(input.val()) == input.attr('min')) {
          $(this).attr('disabled', true);
        }

      } else if (type == 'plus') {

        if (currentVal < input.attr('max')) {
          input.val(currentVal + 1).change();
        }
        if (parseFloat(input.val()) == input.attr('max')) {
          $(this).attr('disabled', true);
        }

      }
    } else {
      input.val(0);
    }
  });
  $('body').on('focusin', '.input-number', function () {
    $(this).data('oldValue', $(this).val());
  });
  $('body').on('change', '.input-number', function () {

    var minValue = parseFloat($(this).attr('min'));
    var maxValue = parseFloat($(this).attr('max'));
    var valueCurrent = parseFloat($(this).val());

    var name = $(this).attr('name');
    if (valueCurrent >= minValue) {
      $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
    } else {
      alert('Нельзя заказать меньше ' + minValue);
      $(this).val($(this).data('oldValue'));
    }
    if (valueCurrent <= maxValue) {
      $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
    } else {
      alert('Извините, но больше нету');
      $(this).val($(this).data('oldValue'));
    }


  });

  $('body').on('keydown', ".input-number", function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
      // Allow: Ctrl+A
      (e.keyCode == 65 && e.ctrlKey === true) ||
      // Allow: home, end, left, right
      (e.keyCode >= 35 && e.keyCode <= 39)) {
      // let it happen, don't do anything
      return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
  });

})
