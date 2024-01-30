const stripHtml = function (html) {
  let doc = new DOMParser().parseFromString(html, 'text/html')
  return doc.body.textContent || ""
}

export { stripHtml }