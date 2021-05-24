export const PageMixin = {
  methods: {
    printPage(id, url) {
      const print = document.getElementById('all-print')
      print.setAttribute('src', url)
    }
  }
}
