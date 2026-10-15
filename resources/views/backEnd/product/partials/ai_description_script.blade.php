<script>
(function () {
    function getProductName() {
        return ($('input[name="name"]').val() || '').trim();
    }

    function getCategoryId() {
        return $('#category_id').val() || '';
    }

    function getSummernoteEl() {
        return $('.summernote').first();
    }

    $('#btn-generate-ai-description').on('click', function () {
        var $btn = $(this);
        var name = getProductName();
        var categoryId = getCategoryId();

        if (!name) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('আগে প্রোডাক্টের নাম লিখুন', 'AI Description');
            } else {
                alert('আগে প্রোডাক্টের নাম লিখুন');
            }
            $('input[name="name"]').focus();
            return;
        }

        if (!categoryId) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('আগে ক্যাটাগরি সিলেক্ট করুন', 'AI Description');
            } else {
                alert('আগে ক্যাটাগরি সিলেক্ট করুন');
            }
            $('#category_id').focus();
            return;
        }

        var $editor = getSummernoteEl();
        var currentHtml = $editor.summernote('code') || '';
        var hasContent = $('<div>').html(currentHtml).text().trim().length > 0;

        if (hasContent && !confirm('বিদ্যমান ডেসক্রিপশন প্রতিস্থাপন করে AI ডেসক্রিপশন বসাবেন?')) {
            return;
        }

        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fe-loader"></i> তৈরি হচ্ছে...');

        $.ajax({
            url: '{{ route('products.generate_ai_description') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: name,
                category_id: categoryId
            },
            success: function (res) {
                if (res.success && res.description) {
                    $editor.summernote('code', res.description);
                    if (typeof toastr !== 'undefined') {
                        toastr.success('AI ডেসক্রিপশন যোগ হয়েছে', 'Success');
                    }
                } else {
                    var msg = res.message || 'ডেসক্রিপশন তৈরি ব্যর্থ';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(msg, 'Error');
                    } else {
                        alert(msg);
                    }
                }
            },
            error: function (xhr) {
                var msg = 'ডেসক্রিপশন তৈরি ব্যর্থ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(msg, 'Error');
                } else {
                    alert(msg);
                }
            },
            complete: function () {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
})();
</script>
