import moment from 'moment';

export default {
    filters: {
        formatDateTime(str, format = null, outputFormat = 'YYYY-MM-DD HH:mm:ss') {
            if (format == null) {
                return moment(str).format(outputFormat);
            }

            return moment(str, format).format(outputFormat);
        },

        formatDate(str, format = null, outputFormat = 'YYYY-MM-DD') {
            if (format == null) {
                return moment(str).format(outputFormat);
            }

            return moment(str, format).format(outputFormat);
        },
    },
};
