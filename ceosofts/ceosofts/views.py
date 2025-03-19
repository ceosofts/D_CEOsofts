from django.shortcuts import render
from django.contrib.admin.views.decorators import staff_member_required

@staff_member_required
def admin_dashboard(request):
    """
    Admin dashboard view showing summary of site data
    """
    context = {
        'title': 'Admin Dashboard',
        # Add more context data here as needed
    }
    return render(request, 'admin/dashboard.html', context)
