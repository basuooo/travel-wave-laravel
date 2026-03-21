<template id="map-assignment-template">
    @include('admin.map-sections.partials.assignment-row', ['index' => '__INDEX__', 'assignment' => [], 'assignmentTargets' => $assignmentTargets, 'positionOptions' => $positionOptions])
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const assignmentList = document.getElementById('map-assignments-list');
    const assignmentTemplate = document.getElementById('map-assignment-template');
    const addAssignmentButton = document.getElementById('add-map-assignment');

    const refreshAssignmentRemoval = () => {
        assignmentList?.querySelectorAll('.remove-map-assignment').forEach((button) => {
            button.onclick = () => button.closest('.map-assignment-row')?.remove();
        });
    };

    addAssignmentButton?.addEventListener('click', () => {
        if (!assignmentList || !assignmentTemplate) {
            return;
        }

        const index = assignmentList.children.length;
        assignmentList.insertAdjacentHTML('beforeend', assignmentTemplate.innerHTML.replaceAll('__INDEX__', index));
        refreshAssignmentRemoval();
    });

    refreshAssignmentRemoval();
});
</script>
