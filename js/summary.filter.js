//filter component window
document.addEventListener("DOMContentLoaded", function () {
    const componentToggleButton = document.getElementById('filter-component-checkall');
    let allComponentsChecked = true;

    componentToggleButton.addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('.filter-component-check');

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allComponentsChecked;
        });

        allComponentsChecked = !allComponentsChecked;
        if (allComponentsChecked) {
            componentToggleButton.classList.add("btn-primary");
            componentToggleButton.classList.remove("btn-outline-primary");
        } else {
            componentToggleButton.classList.remove("btn-primary");
            componentToggleButton.classList.add("btn-outline-primary");
        }
    });

    const componentSearchInput = document.getElementById('filter-component-search');

    componentSearchInput.addEventListener('input', function () {
        const searchText = this.value.toLowerCase();
        const checkboxes = document.querySelectorAll('.form-check-component');

        checkboxes.forEach(check => {
            const label = check.querySelector('label').textContent.toLowerCase();
            if (label.includes(searchText)) {
                check.style.display = '';
            } else {
                check.style.display = 'none';
            }
        });
    });
});

//filter block window
document.addEventListener("DOMContentLoaded", function () {
    const blockToggleButton = document.getElementById('filter-block-checkall');
    let allBlocksChecked = true;

    blockToggleButton.addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('.filter-block-check');

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allBlocksChecked;
        });

        allBlocksChecked = !allBlocksChecked;
        if (allBlocksChecked) {
            blockToggleButton.classList.add("btn-primary");
            blockToggleButton.classList.remove("btn-outline-primary");
        } else {
            blockToggleButton.classList.remove("btn-primary");
            blockToggleButton.classList.add("btn-outline-primary");
        }
    });

    const blockSearchInput = document.getElementById('filter-block-search');

    blockSearchInput.addEventListener('input', function () {
        const searchText = this.value.toLowerCase();
        const checkboxes = document.querySelectorAll('.form-check-block');

        checkboxes.forEach(check => {
            const label = check.querySelector('label').textContent.toLowerCase();
            if (label.includes(searchText)) {
                check.style.display = '';
            } else {
                check.style.display = 'none';
            }
        });
    });
});

//filter reset
document.addEventListener("DOMContentLoaded", function () {
    const filterResetButton = document.getElementById('filter-reset');

    filterResetButton.addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('.filter-component-check, .filter-block-check');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });

        const inputs = document.querySelectorAll('#filter-component-search, #filter-block-search, #filter-project-name');
        inputs.forEach(input => {
            input.value = '';
        });

        const checkboxContainers = document.querySelectorAll('.form-check-component, .form-check-block');
        checkboxContainers.forEach(container => {
            container.style.display = '';
        });

        const selects = document.querySelectorAll('.form-select');
        selects.forEach(select => {
            select.value = select.querySelector('option').value;
        });

        let componentToggleButton = document.getElementById('filter-component-checkall');
        let blockToggleButton = document.getElementById('filter-block-checkall');
        blockToggleButton.classList.add("btn-primary");
        blockToggleButton.classList.remove("btn-outline-primary");
        componentToggleButton.classList.add("btn-primary");
        componentToggleButton.classList.remove("btn-outline-primary");
    });
});

//filter apply
document.addEventListener("DOMContentLoaded", function () {
    const filterApplyButton = document.getElementById('filter-apply');

    filterApplyButton.addEventListener('click', function () {
        const needFilterName = document.getElementById('filter-enable-name').checked;
        const needFilterState = document.getElementById('filter-enable-state').checked;
        const needFilterComponents = document.getElementById('filter-enable-component').checked;
        const needFilterBlocks = document.getElementById('filter-enable-block').checked;

        const projectNameFilter = document.getElementById('filter-project-name').value.toLowerCase();
        const projectStateFilter = document.querySelector('.form-select').value;

        const selectedBlocks = Array.from(document.querySelectorAll('.filter-block-check:checked')).map(el => el.value);
        const selectedComponents = Array.from(document.querySelectorAll('.filter-component-check:checked')).map(el => el.value);

        const accordionItems = document.querySelectorAll('.accordion-item');

        accordionItems.forEach(item => {
            let result = true;
            const matchesName = item.getAttribute('data-project-name').toLowerCase().includes(projectNameFilter);
            const matchesState = projectStateFilter === '-1' || item.getAttribute('data-project-state') === projectStateFilter;
            const itemBlocks = item.getAttribute('data-blocks').split(',');
            const itemComponents = item.getAttribute('data-components').split(',');
            const matchesBlocks = selectedBlocks.length > 0 && selectedBlocks.some(block => itemBlocks.includes(block));
            const matchesComponents = selectedComponents.length > 0 && selectedComponents.some(component => itemComponents.includes(component));

            if (result && needFilterName) {
                result = matchesName;
            }

            if (result && needFilterState) {
                result = matchesState;
            }

            if (result && needFilterComponents) {
                result = matchesComponents;
            }

            if (result && needFilterBlocks) {
                result = matchesBlocks;
            }

            item.style.display = result ? '' : 'none';
        });
    });
});

//filter category toggle
document.addEventListener("DOMContentLoaded", function () {
    const filterEnableName = document.getElementById('filter-enable-name');

    filterEnableName.addEventListener('change', function () {
        document.getElementById('filter-project-name').disabled = !filterEnableName.checked;
    });

    const filterEnableState = document.getElementById('filter-enable-state');

    filterEnableState.addEventListener('change', function () {
        document.getElementById('filter-project-state').disabled = !filterEnableState.checked;
    });

    const filterEnableComponents = document.getElementById('filter-enable-component');

    filterEnableComponents.addEventListener('change', function () {
        document.getElementById('filter-component-checkall').disabled = !filterEnableComponents.checked;
        document.getElementById('filter-component-search').disabled = !filterEnableComponents.checked;
        document.querySelectorAll('.filter-component-check').forEach(item => {
            item.disabled = !filterEnableComponents.checked;
        });
    });

    const filterEnableBlocks = document.getElementById('filter-enable-block');

    filterEnableBlocks.addEventListener('change', function () {
        document.getElementById('filter-block-checkall').disabled = !filterEnableBlocks.checked;
        document.getElementById('filter-block-search').disabled = !filterEnableBlocks.checked;
        document.querySelectorAll('.filter-block-check').forEach(item => {
            item.disabled = !filterEnableBlocks.checked;
        });
    });
});