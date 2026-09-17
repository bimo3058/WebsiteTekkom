@if($activeRole==='admin')
<x-capstone::button variant="outline" size="sm" x-show="canApprove(event)" @click.stop="approve(event)" ::disabled="saving" class="border-green-200 text-green-600 hover:bg-green-50 hover:text-green-700"><x-capstone::icon name="Check" class="h-3.5 w-3.5" />Approve</x-capstone::button>
<x-capstone::button variant="outline" size="sm" x-show="canApprove(event)" @click.stop="reject(event)" ::disabled="saving" class="border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700"><x-capstone::icon name="X" class="h-3.5 w-3.5" />Reject</x-capstone::button>
@elseif($activeRole==='dosen')
<x-capstone::button variant="ghost" size="sm" x-show="canEdit(event)" @click.stop="edit(event)" class="text-gray-600 hover:text-gray-900"><x-capstone::icon name="Edit" class="h-3.5 w-3.5" />Edit</x-capstone::button>
<x-capstone::button variant="ghost" size="sm" x-show="canEdit(event)" @click.stop="confirmDelete(event)" class="text-red-600 hover:bg-red-50 hover:text-red-700"><x-capstone::icon name="Trash2" class="h-3.5 w-3.5" />Delete</x-capstone::button>
@endif
