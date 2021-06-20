import { EntryMixin } from "./EntryMixin"
import { RecordMixin } from "./RecordMixin"
import { DetailsMixin } from "./DetailsMixin"

export const TransferMixin = {
  mixins: [
    EntryMixin,
    RecordMixin,
    DetailsMixin,
  ],

  data() {
    return {
      payload: {},
      backRoute: ''
    }
  }
}
