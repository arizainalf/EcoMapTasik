const ajaxCall = (url, method, data, successCallback, errorCallback) => {
    const isFormData = data instanceof FormData
    const isGet = method.toUpperCase() === 'GET'

    $.ajax({
        type: method,
        enctype: 'multipart/form-data',
        url,
        cache: false,
        data,
        data: isGet ? data : data,
        contentType: isGet
            ? false
            : isFormData
            ? false
            : 'application/x-www-form-urlencoded; charset=UTF-8',
        processData: isGet ? true : !isFormData,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
        },
        dataType: 'json',
        success: function (response) {
            successCallback(response)
        },
        error: function (error) {
            console.log(error)
            errorCallback(error)
        }
    })
}

const initEditModal = function ({
    formSelector,
    endpoint,
    fields = [],
    callback = null,
    onFetched = null
}) {
    console.log(endpoint)
    $.get(`/${endpoint}`, function (response) {
        const form = $(formSelector)[0]
        const data = response.data
        console.log(data, fields)

        $(formSelector).data('id', data.id)

        // Populate all fields
        fields.forEach(field => {
            const input = $(form).find(`[name="${field}"]`)

            console.log(data[field], field, data)

            if (input.is(':checkbox')) {
                input.prop('checked', data[field])
            } else if (input.is(':radio')) {
                input.filter(`[value="${data[field]}"]`).prop('checked', true)
            } else {
                input.val(data[field])
            }
        })

        // Run optional custom logic (icon, select2, etc)
        if (typeof callback === 'function') {
            callback(data)
        }

        // Either run custom modal open logic, or fallback to default
        if (typeof onFetched === 'function') {
            onFetched(data)
        }
    })
}

window.initDetailModal = function ({
    modalId,
    endpoint,
    fields = [],
    callback = null,
    onFetched = null
}) {
    console.log(endpoint)

    $.get(`/${endpoint}`, function (response) {
        const data = response.data
        console.log(data, fields)

        // Isi elemen berdasarkan ID
        fields.forEach(field => {
            const elementId = `#${field}-detail`
            const el = $(elementId)

            if (el.length) {
                if (el.is('img')) {
                    el.attr('src', data[field])
                } else {
                    el.text(data[field])
                }
            } else {
                console.warn(`Elemen ${elementId} tidak ditemukan.`)
            }
        })

        // Callback khusus jika ada
        if (typeof callback === 'function') {
            callback(data)
        }

        // Buka modal setelah data siap
        if (typeof onFetched === 'function') {
            onFetched(data)
        } else {
            openModal(modalId)
        }
    })
}

function formatRupiah (amount, decimal = 0) {
    if (typeof amount !== 'number') amount = parseFloat(amount)
    if (isNaN(amount)) return 'Rp. 0'

    return (
        'Rp. ' +
        amount
            .toFixed(decimal)
            .replace(/\d(?=(\d{3})+(?!\d))/g, '$&.')
            .replace('.', ',')
    )
}

function formatBerat (beratDalamGram) {
    if (typeof beratDalamGram !== 'number')
        beratDalamGram = parseFloat(beratDalamGram)
    if (isNaN(beratDalamGram)) return '0 gram'

    if (beratDalamGram >= 1000) {
        const beratDalamKg = beratDalamGram / 1000
        return beratDalamKg % 1 === 0
            ? beratDalamKg + ' kg'
            : beratDalamKg.toFixed(2) + ' kg'
    }
    return beratDalamGram + ' gram'
}

function formatTanggal (timestamp) {
    const tanggal = new Date(timestamp)
    const options = { day: 'numeric', month: 'long', year: 'numeric' }
    return tanggal.toLocaleDateString('id-ID', options)
}

function showToast (icon = 'success', message) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: toast => {
            toast.onmouseenter = Swal.stopTimer
            toast.onmouseleave = Swal.resumeTimer
        }
    })
    Toast.fire({
        icon: icon,
        title: message
    })
}

function errorToast (error) {
    console.log(error)
    showToast('error', error.responseJSON.message ? error?.message ? error : error.responseJSON.message : '-')
}
function successToast (response) {
    showToast('success', response?.message || '-')
}

function showSwal (title, icon, message, redirect = null) {
    swal.fire({
        title: title,
        icon: icon,
        text: message,
        theme: getSavedTheme(),
        timer: 2000,
        buttons: false
    }).then(function () {
        if (redirect) {
            window.location.href = redirect
        }
    })
}

const handleSuccess = (response, modalId = null, redirect = null) => {
    showSwal('Berhasil', 'success', response.message, redirect)

    if (modalId !== null) {
        $(`#${modalId}`).modal('hide')
    }
}

function getSavedTheme () {
    return localStorage.getItem('theme') || 'light'
}

const handleValidationErrors = (error, formId = null, fields = null) => {
    if (error.responseJSON.data && fields) {
        fields.forEach(field => {
            if (error.responseJSON.data[field]) {
                $(`#${formId} #${field}`).addClass('is-invalid')
                $(`#${formId} #error${field}`).html(
                    error.responseJSON.data[field][0]
                )
            } else {
                $(`#${formId} #${field}`).removeClass('is-invalid')
                $(`#${formId} #error${field}`).html('')
            }
        })
    } else {
        console.log(error.responseJSON.message)
        const errors = error.responseJSON.message || error?.message || '-'
        showSwal('Gagal', 'error', errors)
    }
}

const handleSimpleError = error => {
    const errors = error.responseJSON.message || error?.message || '-'
    showSwal('Gagal', 'error', errors)
}

const confirmApprove = (url, tableId) => {
    swal.fire({
        title: 'Apakah Kamu Yakin?',
        text: 'Konfirmasi Pengembalian Buku Ini?',
        icon: 'warning',
        buttons: true,
        dangerMode: true
    }).then(willApprove => {
        if (willApprove) {
            const data = null

            const successCallback = function (response) {
                handleSuccess(response, tableId, null)
            }

            const errorCallback = function (error) {
                console.log(error)
            }

            ajaxCall(url, 'GET', data, successCallback, errorCallback)
        }
    })
}

const setButtonLoadingState = (buttonSelector, isLoading, title = 'Simpan') => {
    const buttonText = isLoading
        ? `<i class="fas fa-spinner fa-spin mr-1"></i> ${title}`
        : title
    $(buttonSelector).prop('disabled', isLoading).html(buttonText)
}

function confirmLogout (url) {
    Swal.fire({
        title: 'Konfirmasi Logout',
        text: 'Apakah Anda yakin ingin keluar dari sistem?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal',
        buttonsStyling: false, // Nonaktifkan style default SweetAlert
        customClass: {
            confirmButton: 'btn btn-lg btn-danger ml-4', // Class Bootstrap untuk tombol konfirmasi
            cancelButton: 'btn btn-lg btn-primary' // Class Bootstrap untuk tombol batal
        },
        reverseButtons: true // Tombol "Ya" di kanan, "Batal" di kiri
    }).then(result => {
        if (result.isConfirmed) {
            // Submit form logout
            const data = null

            const successCallback = function (response) {
                handleSuccess(response, null, '/')
            }

            const errorCallback = function (error) {
                handleSimpleError(error)
            }

            ajaxCall(url, 'POST', data, successCallback, errorCallback)
        }
    })
}

function loadData (selector, url, page = 1, perPage = 25, query = '') {
    console.log('load data')
    $.ajax({
        url: `/${url}?page=${page}&search=${encodeURIComponent(
            query
        )}&perPage=${perPage}`,
        type: 'GET',
        success: function (res) {
            $(selector).html(res.data.view)
            $('#pagination-links').html(res.data.pagination)
            // update state
            currentPage = page
            currentQuery = query
        },
        error: function () {
            showToast('error', 'Gagal memuat data')
        }
    })
}

const loadSelectOptions = (selector, url, selectedValue = null) => {
    const selectElem = $(selector)

    // Kosongkan dulu opsi yang ada
    selectElem.empty()

    // Tambah opsi kosong dulu
    const emptyOption = $('<option></option>')
        .attr('value', '')
        .text('-- Pilih Data --')
    selectElem.append(emptyOption)

    const successCallback = function (response) {
        console.log(response)
        const responseList = response.data
        responseList.forEach(row => {
            const option = $('<option></option>')
                .attr('value', row.id)
                .text(
                    row.cost
                        ? `${row.cost} - ${row.service} - ${row.etd}`
                        : row.label ?? row.nama ?? row.judul ?? row.name
                )
            selectElem.append(option)
        })

        // Set pilihan default kalau ada
        if (selectedValue !== null) {
            selectElem.val(selectedValue)
        }
    }

    const errorCallback = function (error) {
        console.error(error)
    }

    const data = {
        mode: 'select'
    }

    ajaxCall(url, 'GET', data, successCallback, errorCallback)
}

const loadSelectWilayah = (selector, url, selectedValue = null) => {
    const selectElem = $(selector)

    // Kosongkan dulu opsi yang ada
    selectElem.empty()

    // Tambah opsi kosong dulu
    const emptyOption = $('<option></option>')
        .attr('value', '')
        .text('-- Pilih Data --')
    selectElem.append(emptyOption)

    const successCallback = function (response) {
        console.log(response)
        const responseList = response.data
        responseList.forEach(row => {
            console.log(row)
            const option = $('<option></option>')
                .attr('value', row.id)
                .text(row.label ?? row.nama ?? row.name)
            selectElem.append(option)
        })

        // Set pilihan default kalau ada
        if (selectedValue !== null) {
            selectElem.val(selectedValue)
        }
    }

    const errorCallback = function (error) {
        console.error(error)
    }

    const data = {
        mode: 'select'
    }

    ajaxCall(url, 'GET', data, successCallback, errorCallback)
}

const updateJam = () => {
    let jam = new Date()
    $('#jam').html(
        'Jam ' +
            setUpJam(jam.getHours()) +
            ':' +
            setUpJam(jam.getMinutes()) +
            ':' +
            setUpJam(jam.getSeconds())
    )
}

const setUpJam = jam => {
    return jam < 10 ? '0' + jam : jam
}

const togglePasswordVisibility = (inputSelector, iconSelector) => {
    let passwordInput = $(inputSelector)
    let toggleIcon = $(iconSelector)

    console.log(inputSelector, iconSelector)

    if (passwordInput.attr('type') === 'password') {
        passwordInput.attr('type', 'text')
        toggleIcon.removeClass('fas fa-eye').addClass('fas fa-eye-slash')
    } else {
        passwordInput.attr('type', 'password')
        toggleIcon.removeClass('fas fa-eye-slash').addClass('fas fa-eye')
    }
}

function openModal (modalId) {
    const modal = document.getElementById(modalId)
    modal.classList.remove('hidden')
    setTimeout(() => {
        modal.classList.add('show')
    }, 10)
    document.body.style.overflow = 'hidden'
}

function closeModal (modalId) {
    const modal = document.getElementById(modalId)
    modal.classList.remove('show')
    setTimeout(() => {
        modal.classList.add('hidden')
        document.body.style.overflow = 'auto'
    }, 300)
}

function loadCartItems () {
    $.ajax({
        url: '/cart/item/items',
        method: 'GET',
        success: function (response) {
            $('#offcanvasSidebar').html(response)
        },
        error: function (xhr) {
            console.error(xhr.responseText)
        }
    })
}
