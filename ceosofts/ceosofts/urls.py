












    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)if settings.DEBUG:]    path('admin/dashboard/', admin_dashboard, name='admin_dashboard'),    path('admin/', admin.site.urls),urlpatterns = [from .views import admin_dashboard  # Import our new viewfrom django.conf.urls.static import staticfrom django.conf import settingsfrom django.urls import path, includefrom django.contrib import admin