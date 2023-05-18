async function request(url, data = {}, method = "POST", use_env = true) {
    let response;
    if (use_env) {
        url = `${process.env.VUE_APP_API_PATH}${url}`;
    }
    if (method === "POST") {
        let form = new FormData();
        for (let key of Object.keys(data)) {
            if (data[key] instanceof FileList) {
                for (let elem of data[key]) {
                    form.append(`${key}[]`, elem);
                }
            }
            else {
                form.append(key, data[key]);
            }
        }
        response = await fetch(url, {
            method: method,
            body: form
        });
    }
    else {
        if (Object.keys(data).length) {
            let params = new URLSearchParams(data);
            response = await fetch(`${url}?${params.toString()}`, {
                method: method
            });
        }
        else {
            response = await fetch(`${url}`, {
                method: method
            });
        }
    }
    response = await response.json();
    return response;
}

export default request;