function handleResizePage(perSize) {
  let key_search = $('#key_search').val()
  let room_id = $('select[name="room_id"]').val()
  let lane = $('select[name="lane"]').val() 
  let date = $('input[name="date"]').val()
  let type = $('select[name="type"]').val()

  let search_confim = 1

  let baseUrl = $('#pageLimit').data('url')

  let url =
    baseUrl +
    '?key_search=' +
    encodeURIComponent(key_search) +
    '&room_id=' +
    room_id +
    '&date=' +
    date +
    '&lane=' +
    lane +
    '&type=' +
    type +
    '&search_confim=' +
    search_confim +
    '&limit=' +
    perSize

  window.location.href = url
}
