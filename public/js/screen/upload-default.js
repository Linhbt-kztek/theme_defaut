document.addEventListener('DOMContentLoaded', () => {
  const uploadBox = document.getElementById('uploadBox')
  const fileInput = document.getElementById('fileInput')
  const previewWrapper = document.getElementById('previewWrapper')
  const previewImg = document.getElementById('previewImage')
  const previewVideo = document.getElementById('previewVideo')
  const loadingOverlay = document.getElementById('loadingOverlay')
  const percentText = document.getElementById('percentText')

  const allowedImage = ['jpg', 'jpeg', 'png', 'gif', 'webp']
  const allowedVideo = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'm4v']
  const allowedExt = allowedImage.concat(allowedVideo)

  let file_upload = null

  uploadBox.addEventListener('click', () => fileInput.click())

  // Drag events
  uploadBox.addEventListener('dragover', e => {
    e.preventDefault()
    uploadBox.classList.add('dragover')
  })
  uploadBox.addEventListener('dragleave', () => uploadBox.classList.remove('dragover'))
  uploadBox.addEventListener('drop', e => {
    e.preventDefault()
    uploadBox.classList.remove('dragover')
    handleFile(e.dataTransfer.files[0])
  })

  fileInput.addEventListener('change', e => {
    simulateLoading(() => handleFile(e.target.files[0]))
  })

  document.getElementById('editBtn').addEventListener('click', e => {
    e.stopPropagation()
    fileInput.click()
  })

  function handleFile(file) {
    if (!file) return
    const ext = file.name.split('.').pop().toLowerCase()

    // Validate
    if (!allowedExt.includes(ext)) {
      alert('File không hợp lệ')
      fileInput.value = '' // reset
      return
    }

    file_upload = file

    previewWrapper.style.display = 'block'
    document.querySelector('.placeholder-text').style.display = 'none'

    loadingOverlay.style.display = 'flex'
    percentText.textContent = '0%'

    const url = URL.createObjectURL(file)

    if (file.type.startsWith('image')) {
      previewImg.style.display = 'none'
      previewVideo.style.display = 'none'

      previewImg.src = url

      previewImg.onload = () => {
        loadingOverlay.style.display = 'none'
        previewImg.style.display = 'block'
      }
    } else if (file.type.startsWith('video')) {
      previewImg.style.display = 'none'
      previewVideo.style.display = 'none'

      previewVideo.src = url

      previewVideo.onloadeddata = () => {
        loadingOverlay.style.display = 'none'
        previewVideo.style.display = 'block'
      }
    }
  }

  // loading 0 → 100%
  function simulateLoading(callback) {
    let progress = 0
    const progressCircle = document.getElementById('progressCircle')
    const percentText = document.getElementById('percentText')

    loadingOverlay.style.display = 'flex'

    const radius = 45
    const circumference = 2 * Math.PI * radius

    progressCircle.style.strokeDasharray = circumference

    const interval = setInterval(() => {
      progress += 5
      if (progress > 100) progress = 100

      // cập nhật stroke-dashoffset
      const offset = circumference * (1 - progress / 100)
      progressCircle.style.strokeDashoffset = offset

      percentText.textContent = progress + '%'

      if (progress >= 100) {
        clearInterval(interval)
        loadingOverlay.style.display = 'none'
        callback()
      }
    }, 100)
  }

  window.setDefault = function () {
    let screen_id = $('#screen_detail .screen_id').val()
    if (!file_upload) {
      main_layout.alert_main('Vui lòng chọn dữ liệu trình chiếu', 'error', 'center')
      return
    }
    const formData = new FormData()
    formData.append('file', file_upload)
    formData.append('_token', main.token)

    main_layout.show_loader()

    $.ajax({
      url: `/admin/screen/default/save/${screen_id}`,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (result) {
        main_layout.hide_loader()
        main_layout.alert_main(result.message, result.status == 200 ? 'success' : 'error', 'center')

        if (result.status == 200) {
          previewFromUrl(result.url_defaut, result.isVideo)
        }
      },
    })
  }

  window.previewFromUrl = function (url, isVideo = false) {
    loadingOverlay.style.display = 'none'
    if (url) {
      document.querySelector('.placeholder-text').style.display = 'none'
      previewWrapper.style.display = 'block'
      if (!isVideo) {
        previewVideo.style.display = 'none'
        previewImg.src = url
        previewImg.style.display = 'block'
      } else {
        previewImg.style.display = 'none'
        previewVideo.src = url
        previewVideo.style.display = 'block'
      }
    } else {
      previewWrapper.style.display = 'none'
      document.querySelector('.placeholder-text').style.display = 'block'
      previewImg.style.display = 'none'
      previewVideo.style.display = 'none'
    }
  }
})
