class Services {
    constructor() {
        this.url = window.location.origin;
    }
}

class GradeService extends Services {
    //FUNCION PARA GUARDAR NOTA
    async storeGrade(grade, comment, studentId, activity, recordingId, token, idGroup) {
        const response = await $.ajax({
            url: this.url+"/grades",
            type: "POST",
            headers: {'x-csrf-token': token},
            data: {
                grade: grade,
                comment: comment,
                studentId: studentId,
                activity: activity,
                recordingId: recordingId,
                idGroup: idGroup
            },
            cache: false,
            dataType: "json"
        });
        return response;
    }
    //FUNCION PARA PUBLICAR NOTAS EN MOODLE
    async publishGrades(instance, content, groupId, token) {
        const response = await $.ajax({
            url: this.url+"/publishGrades",
            type: "POST",
            headers: {"x-csrf-token": token},
            data: {
                instance: instance,
                content: content,
                groupId: groupId,
            },
            cache: false,
            dataType: "json"
        });
        return response;
    }
}
