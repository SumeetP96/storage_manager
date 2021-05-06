export const DateMixin = {
  methods: {
    formatDate(fieldName) {
      let inputDate = this.record[fieldName]
      if (!inputDate) return

      if (inputDate.search('-') < 0) {
        this.record[fieldName] = ''
        return
      }

      inputDate = inputDate.split('-')
      if (inputDate.length < 2 || inputDate.length > 3) {
        this.record[fieldName] = ''
        return
      }

      if (inputDate.length == 2) {
        if (!inputDate[0] || isNaN(inputDate[0]) || inputDate[0].length > 2
          || parseInt(inputDate[0]) <= 0 || parseInt(inputDate[0]) > 31) {
            this.record[fieldName] = ''
            return
        }

        if (!inputDate[1] || isNaN(inputDate[1]) || inputDate[1].length > 2
          || parseInt(inputDate[1]) <= 0 || parseInt(inputDate[1]) > 12) {
            this.record[fieldName] = ''
            return
        }
      }

      if (inputDate.length == 3) {
        if (!inputDate[2] || isNaN(inputDate[2]) || parseInt(inputDate[2]) <= 0 ||
            inputDate[2].length < 2 || inputDate[2].length == 3 || inputDate[2].length > 4) {
              this.record[fieldName] = ''
              return
        }
      }

      let DD = inputDate[0]
      if (DD.length == 1) DD = '0' + DD

      let MM = inputDate[1]
      if (MM.length == 1) MM = '0' + MM

      let YYYY = ''
      let currentYear = new Date().getFullYear()
      if (inputDate.length == 2) YYYY = currentYear
      if (inputDate.length == 3) {
        if(inputDate[2].length == 2) YYYY = currentYear.toString()[0] + currentYear.toString()[1] + inputDate[2]
        else YYYY = inputDate[2]
      }

      let d = parseInt(DD)
      let m = parseInt(MM)
      let y = parseInt(YYYY)

      if (m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10 || m == 12) {
        if (d > 31) {
          this.record[fieldName] = ''
          return
        }
      }

      if (m == 4 || m == 6 || m == 9 || m == 11) {
        if (d > 30) {
          this.record[fieldName] = ''
          return
        }
      }

      if (m == 2) {
        if ((y % 4) == 0) {
          if (d > 29) {
            this.record[fieldName] = ''
            return
          }
        } else {
          if (d > 28) {
            this.record[fieldName] = ''
            return
          }
        }
      }

      this.record[fieldName] = DD + '-' + MM + '-' + YYYY
    },

    flipToYMD(dateDMY) {
      if (!dateDMY) return ''
      let date = dateDMY.split('-')
      return date[2] + '/' + date[1] + '/' + date[0]
    }
  }
}
