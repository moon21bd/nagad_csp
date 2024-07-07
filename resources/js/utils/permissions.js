import store from '@/store';

export function hasPermissionForPath(path) {
    return store.state.userPermissions.some(permission => permission.path === path);
}
