function searchFunction() {
    let input = document.getElementById("myInput");
    let filter = input.value.trim().toUpperCase();
    let searchableContainers = document.querySelectorAll(".searchable");

    searchableContainers.forEach(container => {
        let items = container.children; // Get all direct children
        let matchFound = false;

        Array.from(items).forEach(item => {
            let text = item.textContent || item.innerText;
            if (text.toUpperCase().includes(filter)) {
                item.style.display = ""; // Show matching item
                matchFound = true;
            } else {
                item.style.display = "none"; // Hide non-matching item
            }
        });

        // Hide the entire container if none of its children match
        container.style.display = matchFound ? "" : "none";
    });
}
